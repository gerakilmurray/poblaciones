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
        kml_file = open(file,'r')
        route = name.split('/')
        file_name = route[len(route)-1]
        process_kml(tmp_dir  + '/' + file_name + 'out.csv'  , kml_file)
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
                kml_file = open(tmp_dir + '/' + file,'r')
                route = name.split('/')
                kml_name = route[len(route)-1]
                process_kml(tmp_dir +  '/' + kml_name + 'out.csv', kml_file)
                kml_file.close()
    
def process_kml(result, kml_file):
        s = BeautifulSoup(kml_file, 'xml')
        with open(result, 'w') as csvfile:
            writer = csv.writer(csvfile, dialect='unix')
            writer.writerow(["Latitude","Longitude","Altitude","GeoJson","Name"])
            doc = Document(s)
            for folder in doc.get_folders():
                for placemark in folder.get_placemarks():
                    for place in placemark.get_places():    
                        row = place.get_row()
                        row.append(placemark.get_name())
                        writer.writerow(row)      

        csvfile.close()           

class Document:
    def __init__(self, xml):
        self.name = ""
        self.descritption = ""
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
        self.descritption = ""
        self.placemarks = []
        self.__parse__(xml)
    
    def __parse__(self,xml):
        for placemark in xml.find_all('Placemark'):
            self.placemarks.append(Placemark(placemark))

    def get_placemarks(self):
        return self.placemarks

class Placemark:
    def __init__(self, xml):
        self.name = ""
        self.descritption = ""
        self.places = []
        self.__parse__(xml)
    
    def __parse__(self,xml):
        self.name = xml.find('name').string
        self.descritption = xml.find('descritption')    
        for point in xml.find_all('Point'):
            self.places.append(Point(point))
        for polygon in xml.find_all('Polygon'):
            self.places.append(Polygon(polygon))
                
    def get_places(self):
        return self.places

    def get_name(self):
        return self.name
        
    def get_description(self):
        return self.descritption
     
class Point:
    def __init__(self, xml):
        self.name = ""
        self.descritption = ""
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
     
class Polygon:
    def __init__(self, xml):
        self.name = ""
        self.descritption = ""
        self.coordinates = []
        self.__parse__(xml)
    
    def __parse__(self,xml):
        for coordinate in xml.find_all('coordinates'):
            for coord_strs in coordinate:
                
                coord_str = coord_str.strip()
                coord_str = coord_str.strip('/t')
                coord_str = coord_str.strip('/n')
                if coord_str != "":
                    self.coordinates.append(Coordinate(coord_str))
     
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
        print(coord_str)
        xyz = coord_str.strip().split(",")
        self.x = xyz[0]
        self.y = xyz[1]
        self.z = xyz[2]
    
    def get_xyz_row(self):
        return [self.x, self.y, self.z]
    
    def get_xy_row(self):
        return [self.x, self.y]
        
if __name__ == "__main__":
    main(sys.argv[1])
    
    