import os
import sys
import subprocess
from tkinter import Tk, filedialog, messagebox, Label, Button


class Procesador:

    def __init__(self, root):
        # Título de la ventana
        root.title("Kmx2Csv")
        # Icono de la ventana, en ico o xbm en Linux
        if sys.platform.startswith('win'):
            root.iconbitmap('@ico.ico')
        else:
            root.iconbitmap('@ico.xbm')
        # root.resizable(0, 0) # Desactivar redimensión de ventana
        Label(root, text="Seleccion el archivo a procesar").pack()
        self.ruta = Label(root, text=".")
        self.ruta.pack()
        Button(root, text="Buscar",
               command=self.busquedaDeArchivo).pack(side="left")
        Button(root, text="Procesar", command=self.procesar).pack(side="left")

    def busquedaDeArchivo(self):
        # abre el explorador de archivos y guarda la seleccion en la variable!
        filePath = filedialog.askopenfilename()
        #self.ruta['text'] = 'filePath'
        self.ruta.config(text=filePath)

    def procesar(self):
        try:
            print(self.ruta['text'])
            params = self.ruta['text'].split('.')
            comando = "python3 kmx2csv.py " + \
                params[1] + " " + self.ruta['text'] + " ."
            print(comando)
            os.system(comando)
            test = subprocess.check_output(comando, shell=True)
            if (params[1] == "" or self.ruta['text'] == ""):
                raise Exception("No se seleccionó un archivo")
            messagebox.showinfo(
                title="Aviso", message="Conversión realizada con éxito")
            messagebox.showinfo(title="Archivos generados", message=test)

        except Exception as e:
            messagebox.showerror(title="Error", message=f"{e}")


root = Tk()
main = Procesador(root)
root.mainloop()
