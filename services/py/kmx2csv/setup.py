#setup.py build
import sys
from tkinter import *
from cx_Freeze import setup, Executable
import os

os.environ['TCL_LIBRARY'] = r'C:\Users\Fernando\AppData\Local\Programs\Python\Python37\tcl\tcl8.6'
os.environ['TK_LIBRARY'] = r'C:\Users\Fernando\AppData\Local\Programs\Python\Python37\tcl\tk8.6'

setup(
    name = "procesador",
    version = "1.0",
    description = "procesador",
    options = {"build_exe": {"includes": [],
    "packages":["tkinter"],
    "include_files": ["ico.ico", "kmx2csv.py",
    r'C:\Users\Fernando\AppData\Local\Programs\Python\Python37\DLLs\tcl86t.dll',
    r'C:\Users\Fernando\AppData\Local\Programs\Python\Python37\DLLs\tk86t.dll']}},
    executables = [Executable("executeRun.py", base = "Win32GUI")])
	