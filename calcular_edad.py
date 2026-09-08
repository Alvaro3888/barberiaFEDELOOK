from datetime import datetime

def calcular_edad(fecha_nacimiento: str) -> int:
    nacimiento = datetime.strptime(fecha_nacimiento, "%Y-%m-%d")
    hoy = datetime.today()
    edad = hoy.year - nacimiento.year - ((hoy.month, hoy.day) < (nacimiento.month, nacimiento.day))
    return edad

if __name__ == "__main__":
    fecha = input("Ingrese su fecha de nacimiento (YYYY-MM-DD): ")
    print(f"Su edad es: {calcular_edad(fecha)} años")
