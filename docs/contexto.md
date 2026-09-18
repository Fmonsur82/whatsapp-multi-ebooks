# Contexto de aplicación Biblioteca por WhatsApp

- Esta aplicación funciona como mecanismo de búsqueda y acceso a contenido de bibliotecas digitales de diferentes instituciones educativas.
- Las instituciones deben tener un método de autenticación que admita parámetros, como proxy o micrositio.
- Cuando a una institución adquiere el servicio de WhatsApp se le crea un formulario de registro al servicio que se comparte con los estudiantes.
- Los datos del estudiante quedan anclados a la institución.
- Cuando un estudiante realiza una búsqueda por WhatsApp el sistema identifica: 
    - Sí está registrado. 
    - En qué institución está registrado.
- El sistema realiza el proceso de búsqueda y envía enlaces de los libros.
- Los enlaces están parametrizados con el sistema de autenticación de la institución.
- Al pulsar sobre el enlace se redirige al sistema de autenticación con el parámetro necesario para llegar al contenido


Entiendo; sin embargo, la asociación manual no es la mejor opción, ya que tenemos más de 9000 usuarios registrados. 
Podríamos aprovechar esta situación para capturar la aceptación de políticas de tratamiento de datos y términos y condiciones. 
Podemos aceptar que los usuarios realicen una operación adicional para confirmar su número o usuario. 
En su próxima interacción le indicaríamos al usuario que para continuar usando la herramienta es necesario que acepte los nuevos términos y condiciones, esto lo llevaría una página de verificación donde debe confirmar su número y aceptar los términos y allí se vincularía los datos de usuario y número.
A nivel de base de datos se agregarían los campos de verificación como: `acepta_pol_priv`, `acepta_tyc`, `acepta_fecha_hora`, `acepta_dispositivo` y `acepta_ip` sin agregar más complejidad, eso también justificaría técnicamente que los usuarios deban hacer este proceso adicional.

Lo que sucede hoy es que cuando no se encuentra el usuario el sistema le pide que se registre nuevamente y cuando el usuario intenta registrarse con su número el sistema le dice que ya existe y finalmente no puede usar el servicio y eso es lo que se quiere evitar.
¿Cuál es tu consideración?


1. únicamente a usuarios que lleguen con BSUID sin teléfono
2. Acepto esa verificación
3. Autorizo
4. Sí, debe enviarse al registro normal, permitiendo crear una cuenta nueva.
5. No debería suceder ya que hay una validación para que no curra.
6. 
    - política de tratamiento de datos: `https://digital-content.co/politica-de-datos/`
        - v3-202607
    - términos y condiciones: `https://wsp-multi.dcsing.com/view/terminos-y-condiciones/`
        - v2-202609
7. Confirmo
8. Esta bien. 
9. Respecto a esto, ¿Qué pasa si un usuario decide hacerlo después? ¿Tendría que pedir otro enlace? Esto agregaría pasos y complejidad, porque no solo dejamos un token firmado, de un solo uso? sin vencimiento