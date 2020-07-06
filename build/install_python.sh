#!/bin/bash
# fileencoding=utf8
# lineends=linux

echo -e "\n### Installing Python Pip ##################################################################################################"
sudo apt install python-pip python3-pip

echo -e "\n### Installing dependencies ################################################################################################"
sudo pip install savReaderWriter ijson numpy bs4 lxml unicodecsv
sudo pip3 install savReaderWriter ijson numpy bs4 lxml unicodecsv
pip install savReaderWriter ijson numpy bs4 lxml unicodecsv
pip3 install savReaderWriter ijson numpy bs4 lxml unicodecsv

echo -e "\n### Installed dependencies Python 2.7 ######################################################################################"
pip freeze

echo -e "\n### Installed dependencies Python 3 ########################################################################################"
pip3 freeze