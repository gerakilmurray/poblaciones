from bs4 import BeautifulSoup
from zipfile import ZipFile
import csv
import shutil
import os
import sys
import re

# python3 kmzToCsv.py test.kmz

def main(file): 
    tmp_dir = os.getcwd() + '/tmp'
    name, extension = os.path.splitext(file)
    if extension == ".kml":
        if (os.path.exists(tmp_dir)):
            shutil.rmtree(tmp_dir)
        os.mkdir(tmp_dir)
        kml_file = open(file,'r', encoding="utf8")
        route = name.split('/')
        file_name = route[len(route)-1]
        process_kml(tmp_dir  + '/' + file_name + '_out.csv'  , kml_file)
        kml_file.close()
    elif extension == ".kmz":
        process_kmz(tmp_dir, file)
    #shutil.rmtree(tmp_dir)
   
def process_kmz(tmp_dir, file_name):
    kmz=  ZipFile(file_name,'r')
    kmz.extractall(tmp_dir)
    for subdir, dirs, files in os.walk(tmp_dir):
        for file in files:
            name, extension = os.path.splitext(file)
            if extension == '.kml':
                kml_file = open(tmp_dir + '/' + file,'r', encoding="utf8")
                route = name.split('/')
                kml_name = route[len(route)-1]
                process_kml(tmp_dir +  '/' + kml_name + 'out.csv', kml_file, tmp_dir)
                kml_file.close()
    
def process_kml(result, kml_file, tmp_dir):
        kml_file = kml_file.read().replace("’","'").replace("“","\"").replace("”","\"")
        s = BeautifulSoup(kml_file, 'xml')
        with open(result, 'w') as csvfile:
            writer = csv.writer(csvfile, dialect='unix')
            writer.writerow(["Name","Latitude","Longitude","Altitude","GeoJson","ExtendedData"])
            doc = Document(s)
            for folder in doc.get_folders():
                print(folder.get_name())
                with open(tmp_dir +  '/' + folder.get_name() + 'out.csv', 'w') as acsvfile:
                    awriter = csv.writer(acsvfile, dialect='unix')
                    for placemark in folder.get_placemarks():
                        for place in placemark.get_places():    
                            row = place.get_row()
                            row.insert(0,placemark.get_name())
                            row.insert(5,placemark.get_extended_data())
                            writer.writerow(row)
                            awriter.writerow(row)
                acsvfile.close()
        csvfile.close()           

class Document:
    def __init__(self, xml):
        self.name = ""
        self.description = ""
        self.folders = []
        self.__parse__(xml)
    
    def __parse__(self,xml):
        for folder in xml.find_all('Folder'):
            self.folders.append(Folder(folder))
    
    def get_folders(self):
        return self.folders

class Folder:
    def __init__(self, xml):
        self.name = ""
        self.description = ""
        self.placemarks = []
        self.__parse__(xml)
    
    def __parse__(self,xml):
        self.description = xml.find('description')   
        self.name = xml.find('name')    		
        for placemark in xml.find_all('Placemark'):
            self.placemarks.append(Placemark(placemark))

    def get_name(self):
        return self.name.text

    def get_placemarks(self):
        return self.placemarks
		
    def get_description(self):
        return self.description

class Placemark:
    def __init__(self, xml):
        self.name = ""
        self.description = ""
        self.places = []
        self.extended_data = []
        self.__parse__(xml)
    
    def __parse__(self,xml):
        self.name = xml.find('name').string
        self.description = xml.find('description')
        for point in xml.find_all('Point'):
            self.places.append(Point(point))
        for polygon in xml.find_all('Polygon'):
            self.places.append(Polygon(polygon))
        for address in xml.find_all('address'):
            self.places.append(Address(address))
        for extended_data in xml.find_all('ExtendedData'):
            self.extended_data.append(ExtendedData(extended_data).get_data()) 
		
    def get_places(self):
        return self.places

    def get_name(self):
        return self.name
        
    def get_description(self):
        return self.description
    
    def get_extended_data(self):
        return self.extended_data
     
class Point:
    def __init__(self, xml):
        self.name = ""
        self.description = ""
        self.coordinates = []
        self.__parse__(xml)
    
    def __parse__(self,xml):
        for coordinate in xml.find_all('coordinates'):
            for coord_str in coordinate:
                self.coordinates.append(Coordinate(coord_str))
             
    def get_row(self):
        row = self.coordinates[0].get_xyz_row()     
        row.append(self.__get_geodata())
        return row     

    def __get_geodata(self):
        coords = []
        for coord in self.coordinates:
            coords.append(coord.get_xyz_row())
        return {
        	"type": "Point",
            "coordinates": [ coords ]
        }
		
class Address:
    def __init__(self, xml):
        self.name = ""
        self.description = ""
        self.coordinates = []
        self.__parse__(xml)
    
    def __parse__(self,xml):
        xy = xml.text.strip().split(" ")
        coord_str = xy[0] + "," + xy[1] + ",0"
        self.coordinates.append(Coordinate(coord_str))
             
    def get_row(self):
        row = self.coordinates[0].get_xyz_row()     
        row.append(self.__get_geodata())
        return row     

    def __get_geodata(self):
        coords = []
        for coord in self.coordinates:
            coords.append(coord.get_xyz_row())
        return {
        	"type": "Point",
            "coordinates": [ coords ]
        }
		
class Polygon:
    def __init__(self, xml):
        self.name = ""
        self.description = ""
        self.coordinates = []
        self.__parse__(xml)
    
    def __parse__(self,xml):
        for coordinate in xml.find_all('coordinates'):
            for coord_strs in coordinate:                
                coord_str = coord_strs.split(' ')
                for string_with_coordinate in coord_str:
                    string_with_coordinate = string_with_coordinate.strip('\n')
                    if string_with_coordinate != "":
                        self.coordinates.append(Coordinate(string_with_coordinate))
     
    def get_row(self):
        row = ["","",""]
        row.append(self.__get_geodata())
        return row
     
    def get_coordinates(self):
        return self.coordinates
        
    def __get_geodata(self):
        coords = []
        for coord in self.coordinates:
            coords.append(coord.get_xy_row())
        return {
        	"type": "Polygon",
            "coordinates": [ coords ]
        }
   
class Coordinate:
    def __init__(self, coord_str):
        xyz = coord_str.strip().split(",")
        self.x = xyz[0]
        self.y = xyz[1]
        self.z = xyz[2]
    
    def get_xyz_row(self):
        return [self.x, self.y, self.z]
    
    def get_xy_row(self):
        return [self.x, self.y]
        
class ExtendedData:
    def __init__(self,xml):
        self.data = []
        self.__parse__(xml)

    def __parse__(self, xml):
        for data in xml.find_all('Data'):
            data_value = data.find('value').contents
            if len(data_value) != 0 :
                data_value = data_value[0]
                data_value = data_value.replace('\n','')
                data_value = data_value.replace('\xa0','')
                self.data.append({"Information":data["name"],"Value":data_value})
            
            
    def get_data(self):
        return self.data

    def get_row(self):
        return self.data


if __name__ == "__main__":
    main(sys.argv[1])



    
    