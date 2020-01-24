from bs4 import BeautifulSoup
import csv

def split_coordinates(str):
    print(str)
    space_splits = str.strip().split(",")
    return [space_splits[1],space_splits[0],space_splits[2]]

def main():
    with open('test.kml', 'r') as f:
        s = BeautifulSoup(f, 'xml')
        with open('out.csv', 'w') as csvfile:
            writer = csv.writer(csvfile)
            for coords in s.find_all('coordinates'):
                writer.writerow(split_coordinates(coords.string))

if __name__ == "__main__":
    main()