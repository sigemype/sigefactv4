# Módulo de Farmacia

---

- [Activación del módulo](#section-1)
- [Configuración de empresa](#section-2)
- [Creación de producto](#section-3)
- [Lista de productos](#section-4)
- [Importar Productos](#section-5)
- [Exportar lista para DIGEMID](#section-6)

<a name="section-1"></a>
## Activación del módulo

### Desde Admin

Es posible habilitar el modulo directamente desde la sección de módulos de usuario en el admin
![image](https://gitlab.com/carlomagno83/facturadorpro4/uploads/adf2673428c84d727d859daf5206252d/image.png)

Al momento de darle permisos al usuario, la configuración para farmacia quedará habilitada. También si se le quita el acceso al usuario se desactivará.

### Desde la configuración

Es posible habilitar el modulo desde la configuración https://demo.facturalo.pro/advanced buscando el selector para "Habilita elementos de farmacia" en la pestaña visual de la configuración avanzada

![image](https://gitlab.com/carlomagno83/facturadorpro4/-/wikis/uploads/3644f7df11cc5631c618dd8e5c4c05d7/image.png)

<a name="section-2"></a>
## Configuración de empresa

En los datos de empresa, para la dirección https://demo.facturalo.pro/companies/create. se habilita "Datos de farmacia" el cual corresponde a "Código de observación DIGEMID"

![image](https://gitlab.com/carlomagno83/facturadorpro4/-/wikis/uploads/d3397282a08690a15075263308b0e71e/image.png)

Este datos se utilizará mas adelante.

<a name="section-3"></a>
## Creación de producto

cuando se crea un producto desde este apartado, se requerirá el código DIGEMID y el Registro sanitario.
![image](https://gitlab.com/carlomagno83/facturadorpro4/-/wikis/uploads/5dbb4ab15ae675551bce7ba8c1d119a2/image.png)

> {danger} debe registrar sus propios productos con la información correcta antes de continuar con los siguientes pasos

<a name="section-4"></a>
## Lista de productos  DIGEMID

https://demo.facturalo.pro/digemid

![image](https://gitlab.com/carlomagno83/facturadorpro4/-/wikis/uploads/15d34913608f8db4e9bd0cabc79084c9/image.png)

Se habilita del modulo de Farmacia, productos, para listar todos los productos de farmacia disponibles.


<a name="section-5"></a>
## Importar Productos para exportación

Se usa como base el archivo facilitado por DIGEMIDen http://opm.digemid.minsa.gob.pe/#/consulta-producto en la sección "Menu Principal" haciendo click en el "Catálogo de productos farmacéuticos"

![image](https://gitlab.com/carlomagno83/facturadorpro4/-/wikis/uploads/cf633794be36235e79f60ec0c7a928cc/image.png)

al descargar el archivo xlsx. se procede a importarlo en la plataforma, en la sección Importar

![image](https://gitlab.com/carlomagno83/facturadorpro4/-/wikis/uploads/3a9c9814777e3184e637a932b99047c9/image.png)

Esto marca el producto para su futura exportación  de forma automática.

![image](https://gitlab.com/carlomagno83/facturadorpro4/-/wikis/uploads/43fcc86d85314eea35fbb2320e94f30d/image.png)
<br>solo se considerará los productos registrados en el sistema

<a name="section-6"></a>
## Exportar lista para DIGEMID

![image](https://gitlab.com/carlomagno83/facturadorpro4/-/wikis/uploads/019931ad841ab9958f54daa65e2b632c/image.png)

Se genera un archivo xls con la siguiente estructura. Solo se exportarán los productos que fueron seleccionados para este fin.


| CodEstab | CodProd | Precio 1 | Precio 2 |
|----------|---------|----------|----------|
| 0023986  | 48883   | 13,00    | 2,00     |


