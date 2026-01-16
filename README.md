# Another Custom Metrics (ACM)

**Another Custom Metrics** es un plugin de WordPress ligero y modular diseñado para crear, gestionar y mostrar tarjetas de estadísticas, contadores animados y métricas destacadas en tu sitio web. Utiliza un sistema de *Custom Post Types* (CPT) para la gestión y *Shortcodes* para la visualización.

## 🚀 Características Principales

* **Gestión Nativa:** Utiliza la interfaz de WordPress para gestionar métricas como si fueran entradas.
* **Vista Previa en Vivo:** Visualiza cómo quedará tu métrica en el panel de administración antes de guardar, gracias a la integración AJAX.
* **Formatos Inteligentes:** Soporte automático para monedas, porcentajes, formatos compactos (1k, 1M), peso y fechas.
* **Animaciones:** 4 tipos de animaciones de entrada (Conteo numérico, Tragamonedas/Slot, Desenfoque y Rebote).
* **Iconografía:** Subida de iconos personalizados con opción de coloreado mediante *CSS Masking*.
* **Rendimiento:** Carga diferida de animaciones mediante `IntersectionObserver` (solo se animan cuando son visibles). No depende de jQuery.
* **Diseño Modular:** Control total sobre colores, tamaños (en `rem`), bordes y disposición (iconos arriba, abajo, izquierda o derecha).

---

## 🛠 Instalación

1.  Sube la carpeta `another-custom-metrics-wp` al directorio `/wp-content/plugins/` de tu instalación de WordPress.
2.  Activa el plugin desde el menú **Plugins** en WordPress.
3.  Verás un nuevo menú llamado **Custom Metrics** en la barra lateral.

---

## 📖 Manual de Uso

### 1. Crear una Nueva Métrica

1.  Ve a **Custom Metrics > Añadir Nueva**.
2.  Ingresa un título (solo para referencia interna).
3.  Configura las opciones en el panel **Configuración de la Métrica**:

#### A. Contenido Principal
* **Valor:** El dato numérico o texto a mostrar.
* **Etiqueta:** El título o descripción debajo del valor (ej. "Clientes Felices").
* **Tamaños:** Ajusta el tamaño de fuente del valor y la etiqueta en unidades `rem`.
* **Prefijo/Sufijo:** Texto que acompaña al valor (ej. "$", "%", "unid.").
* **URL de Destino:** Si se completa, la tarjeta completa funcionará como un enlace.

#### B. Iconografía & Diseño
* **Imagen/Icono:** Sube una imagen desde la biblioteca de medios.
* **Disposición:** Elige dónde se ubica el icono respecto al texto (Arriba, Izquierda, Derecha, Abajo).
* **Colorear Icono:** Si seleccionas un color, el sistema aplicará una máscara CSS para teñir tu icono (funciona mejor con PNGs transparentes o SVGs).

#### C. Formato y Animación
* **Formato de Datos:**
    * *Texto General:* Sin formato.
    * *Número Simple:* Agrega separadores de miles.
    * *Moneda:* Agrega signo $ y formato monetario.
    * *Compacto:* Convierte 1500 en 1.5k.
    * *Peso:* Convierte gramos a kg o toneladas.
* **Animación:**
    * *Conteo:* Cuenta progresiva del 0 al valor final.
    * *Slot:* Efecto de rodillo de tragamonedas (solo números).
    * *Blur:* Revelado con desenfoque.
    * *Rebote:* Efecto de zoom y rebote.

#### D. Apariencia
* Personaliza los colores del valor, etiqueta, fondo y borde de la tarjeta.

---

## 💻 Shortcodes

El plugin ofrece dos shortcodes principales para mostrar las métricas en el frontend.

### 1. Métrica Individual (`[acm_widget]`)

Muestra una sola tarjeta de métrica. Puedes copiar este código directamente desde el listado de métricas o el editor de la métrica.

**Sintaxis:**
```shortcode
[acm_widget id="123"]

```

* **id**: (Requerido) El ID del post de la métrica.

### 2. Grupo de Métricas (`[acm_group]`)

Permite mostrar múltiples métricas en una cuadrícula responsiva (Grid). Además, permite **sobrescribir** estilos globales para unificar el diseño de todas las tarjetas del grupo.

**Sintaxis Básica:**

```shortcode
[acm_group ids="10,12,15" cols="3" gap="20px"]

```

**Atributos Disponibles:**

| Atributo | Descripción | Ejemplo |
| --- | --- | --- |
| `ids` | Lista de IDs de métricas separados por comas. | `ids="10,20,30"` |
| `cols` | Número de columnas en escritorio (1-12). | `cols="3"` |
| `gap` | Espacio entre tarjetas. | `gap="2rem"` |
| `color` | Sobrescribe el color del valor principal. | `color="#ff0000"` |
| `label_color` | Sobrescribe el color de la etiqueta. | `label_color="#333333"` |
| `bg_color` | Sobrescribe el color de fondo. | `bg_color="#f0f0f0"` |
| `value_size` | Sobrescribe el tamaño del valor (rem). | `value_size="4"` |
| `label_size` | Sobrescribe el tamaño de la etiqueta (rem). | `label_size="1.2"` |
| `icon_color` | Sobrescribe el color del icono. | `icon_color="#000000"` |

**Ejemplo Avanzado:**
Crea una fila de 4 columnas donde todas las métricas tengan el texto azul y fondo blanco, ignorando su configuración individual:

```shortcode
[acm_group ids="5,8,12,19" cols="4" color="#0073aa" bg_color="#ffffff" label_color="#555555"]

```

---

## ⚙️ Detalles Técnicos para Desarrolladores

* **Hooks:** El CPT se registra como `acm_widget`.
* **Assets:**
* El CSS frontend (`style.css`) pesa muy poco y usa Flexbox/Grid.
* El JS frontend (`core.js` + `frontend.js`) es Vanilla JS puro.


* **Clases CSS:**
* Las tarjetas tienen la clase `.acm-widget-card`.
* Las disposiciones usan modificadores como `.acm-layout-left`, `.acm-layout-top`, etc.
