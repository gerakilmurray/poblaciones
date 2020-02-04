from bs4 import BeautifulSoup
from zipfile import ZipFile
import csv
import shutil
import os
import sys

# python3 kmzToCsv.py test.kmz

def split_coordinates(str):
    space_splits = str.strip().split(",")
    return [space_splits[0],space_splits[1],space_splits[2]]

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
            writer.writerow(["Latitude","Longitude","Altitude","Name"])
            for coords in s.find_all('Placemark'):
                coordinates = coords.find('coordinates')
                name = coords.find('name')
                if coordinates is not None:
                    row = split_coordinates(coordinates.string)
                    if name is not None:
                        row.append(name.string)
                    writer.writerow(row)
        csvfile.close()           
   
if __name__ == "__main__":
    main(sys.argv[1])
    
    