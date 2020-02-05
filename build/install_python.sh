#!/bin/bash
# fileencoding=utf8
# lineends=linux

echo -e "\n### Installing Python Pip ##################################################################################################"
sudo apt install python-pip

echo -e "\n### Installing dependencies ################################################################################################"
sudo pip install savReaderWriter ijson numpy

echo -e "\n### Installing dependencies ################################################################################################"
pip freeze