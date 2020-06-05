import os
from tkinter import *
from tkinter import filedialog
from tkinter import messagebox
import subprocess

class Procesador:

    def __init__(self, root):
        root.title("Kmx2Csv")     # Título de la ventana 
        root.iconbitmap('ico.ico')  # Icono de la ventana, en ico o xbm en Linux
        #root.resizable(0, 0)         # Desactivar redimensión de ventana  
        label = Label(root, text="Seleccion el archivo a procesar").pack()
        self.ruta = Label(root, text=".")
        self.ruta.pack()
        busqueda = Button(root, text="Buscar", command=self.busquedaDeArchivo).pack(side="left")
        procesar = Button(root, text="Procesar", command=self.procesar).pack(side="left")

    def message_on_click(self):
        messagebox.showinfo(title="asdtitulo", message="asd")
        messagebox.showerror(title="asdtitulo", message="asd")

    def busquedaDeArchivo(self): 
        filePath = filedialog.askopenfilename() #abre el explorador de archivos y guarda la seleccion en la variable!  
        #self.ruta['text'] = 'filePath'
        self.ruta.config(text=filePath)

    def procesar(self):
        try: 
            print(self.ruta['text'])
            params = self.ruta['text'].split('.')
            comando = "py kmx2csv.py " + params[1] + " " + self.ruta['text'] + " ." 
            print(comando)
            os.system(comando)
            print("ACAAAAA")
            test= subprocess.check_output(comando, shell=True);
            if (params[1] == "" or self.ruta['text'] == ""):
               raise Exception("No se seleccionó un archivo")
            messagebox.showinfo(title="Aviso", message="Conversión realizada con éxito")

        except Exception as e:
            messagebox.showerror(title="Error", message=f"{e}")


        
root = Tk()
main = Procesador(root)
root.mainloop() 
