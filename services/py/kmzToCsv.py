from bs4 import BeautifulSoup
from zipfile import ZipFile
import csv
import shutil
import os

def split_coordinates(str):
    space_splits = str.strip().split(",")
    return [space_splits[0],space_splits[1],space_splits[2]]


def main():
    process_kmz('test.kmz')
    #process_kml('test.kml')

def process_kmz(file_name):
    kmz=  ZipFile(file_name,'r')
    tmp_dir = os.getcwd() + '/tmp'
    kmz.extractall(tmp_dir)
    for subdir, dirs, files in os.walk(tmp_dir):
        for file in files:
            name, extension = os.path.splitext(file)
            if extension == '.kml':
                process_kml(tmp_dir + '/' + file)
    #shutil.rmtree(tmp_dir)
    
def process_kml(file_name):
    with open(file_name, 'r') as f:
        s = BeautifulSoup(f, 'xml')
        with open(file_name + 'out.csv', 'w') as csvfile:
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
    
if __name__ == "__main__":
    main()
    