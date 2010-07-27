# Enrutamientos, URLs, y Enlaces

Esta sección le proporcionará tener la idea básica de como Kohana crear solicitud de enrutamiento, la generación de URL y enlaces.

## Enrutar

Como se mencionó en la sección [Request Flow] (about.flow), una petición es manejada por la clase [Request], que encuentra alguna coincidencia en el [Route] y carga el controlador adecuado para manejar la petición. Este sistema proporciona una gran flexibilidad, así como un comportamiento predeterminado sentido común.

Si miras en el `APPPATH/bootstrap.php` podrás ver el siguiente código que se ejecuta inmediatamente antes de la solicitud que es entregada a [Request::instance]:

    Route::set('default', '(<controller>(/<action>(/<id>)))')
      ->defaults(array(
        'controller' => 'welcome',
        'action'     => 'index',
      ));

Esto establece por `default` la ruta con un URL con el formato de `(<controller>(/<action>(/<id>)))`. Las fichas rodeado de `<>` son *claves* y los símbolos, rodeado por `()`, las piezas *opcional*  de la URL. En este caso, la URL completa es opcional, por lo que se correspondería con url en blanco y el controlador por defecto y la acción se daría por supuesto que resulta en la clase `Controller_Welcome`, que se carga y, llamando finalmente, a el método `action_index`.

Tenga en cuenta que en las rutas Kohana, todos los caracteres se les permite, aparte de `()<>` y el `/` no tiene ningún significado especial. En la ruta por defecto el directorio `/` se utiliza como un separador estático, pero siempre y cuando la expresión regular tiene sentido no hay ninguna restricción a cómo se puede dar formato a tus rutas.

### Directorios

For organizational purposes you may wish to place some of your controllers in subdirectories. A common case is for an admin backend to your site:
Por motivos de organización es posible que desee colocar algunas de sus controladores en subdirectorios. Un caso común es el sector administrador para el sitio web:

    Route::set('admin', 'admin(/<controller>(/<action>(/<id>)))')
      ->defaults(array(
        'directory'  => 'admin',
        'controller' => 'home',
        'action'     => 'index',
      ));

Esta ruta especifica que el URL debe empezar con `admin` para que coincida con el directorio y es asignada de forma estática a `admin` con los valores predeterminados. Ahora una petición a `admin/users/create` cargaría la clase `Controller_Admin_Users` y la llamada al método `action_create`.

### Patrones

El sistema de rutas Kohana utiliza expresiones regulares de perl compatible. Por defecto las claves (rodeado con `<>`) están acompañadas de `[a-zA-Z0-9_]++`, sino que puede definir sus propios patrones para cada clave haciendo pasar una matriz asociativa de las llaves y los patrones como un adicional argumento [Route::set]. Para ampliar el ejemplo anterior supongamos que usted tiene una sección de administración y una sección de afiliados. Usted podría especificar a cuáles de las rutas entrar por separado, se puede hacer algo como esto:

    Route::set('sections', '<directory>(/<controller>(/<action>(/<id>)))',
      array(
        'directory' => '(admin|affiliate)'
      ))
      ->defaults(array(
        'controller' => 'home',
        'action'     => 'index',
      ));
      
Esto le proporcionará dos secciones de su sitio, 'admin' y 'afiliado' que le permiten organizar los controladores para cada uno en subdirectorios, sobrescribiendo la ruta por defecto.

### Más ejemplos de Enrutamientos

Hay un sinnúmero de otras posibilidades de rutiar URL. Éstos son algunos ejemplos más:

    /*
     * Autenticación de accesos directos
     */
    Route::set('auth', '<action>',
      array(
        'action' => '(login|logout)'
      ))
      ->defaults(array(
        'controller' => 'auth'
      ));
      
    /*
     * Multi-formato de feeds
     *   452346/comments.rss
     *   5373.json
     */
    Route::set('feeds', '<user_id>(/<action>).<format>',
      array(
        'user_id' => '\d+',
        'format' => '(rss|atom|json)',
      ))
      ->defaults(array(
        'controller' => 'feeds',
        'action' => 'status',
      ));
    
    /*
     * Páginas estáticas
     */
    Route::set('static', '<path>.html',
      array(
        'path' => '[a-zA-Z0-9_/]+',
      ))
      ->defaults(array(
        'controller' => 'static',
        'action' => 'index',
      ));
      
    /*
     * ¿No te gusta barras?
     *   EditGallery:bahamas
     *   Watch:wakeboarding
     */
    Route::set('gallery', '<action>(<controller>):<id>',
      array(
        'controller' => '[A-Z][a-z]++',
        'action'     => '[A-Z][a-z]++',
      ))
      ->defaults(array(
        'controller' => 'Slideshow',
      ));
      
    /*
     * Búsqueda rápida
     */
    Route::set('search', ':<query>', array('query' => '.*'))
      ->defaults(array(
        'controller' => 'search',
        'action' => 'index',
      ));

Las rutas están igualados en el orden especificado fin de ser conscientes que si has puesto las rutas después de los módulos se han cargado un módulo podría especificar una ruta que entra en conflicto con el suyo propio. Esta es también la razón por la que la ruta por defecto es el último conjunto, de modo que las rutas personalizadas se probó por primera vez.
      
### Solicitud de Parámetros

El directorio, el controlador y la acción se puede acceder desde la instancia [Request] en cualquiera de estas dos maneras:

    $this->request->action;
    Request::instance()->action;
    
Todas las teclas que se especifique en una ruta se puede acceder desde dentro del controlador a través de:

    $this->request->param('key_name');
    
La solicitud [Request::param] toma un segundo argumento opcional para especificar un valor de retorno por defecto en caso de que la clave no está fijado por la ruta. Si no se pasan argumentos, todas las llaves se devuelven como una array asociativa.

### Convención

La convención establecida es colocar bien sus rutas personalizadas en el `MODPATH/<module>/init.php` el archivo del módulo para saber si las rutas pertenecen a un módulo, o simplemente insertar en el `APPPATH/bootstrap.php` el archivo anterior, la ruta por defecto si son específicos de la aplicación. Por supuesto, también podrían ser incluidos en un archivo externo o incluso generado dinámicamente.
    
## URLs

Junto con las potentes capacidades de enrutamiento de Kohana se incluyen algunos métodos para la generación de direcciones URL. Siempre se puede especificar su urls como una cadena usando [URL::site] para crear una dirección URL completa de este modo:

    URL::site('admin/edit/user/'.$user_id);

Sin embargo, Kohana también proporciona un método para generar el URL de la definición de la ruta. Esto es muy útil si su ruta nunca podría cambiar, ya que le exime de tener que volver a través de su código y el cambio en todas partes que especificó una URL como una cadena. He aquí un ejemplo de la generación dinámica, que corresponde a los `feeds` ejemplo:

    Route::get('feeds')->uri(array(
      'user_id' => $user_id,
      'action' => 'comments',
      'format' => 'rss'
    ));

Digamos que usted decidió más tarde hacer que la definición de la ruta sea más detallado al cambiarlo a `feeds/<user_id>(/<action>).<format>`. Si escribió el código con el método de generación url por encima de usted no tendría que cambiar una sola línea! Cuando una parte de la URI se encierra entre paréntesis y se especifica una clave que adolecen de ningún valor proporcionado para la generación de url y no tiene valor predeterminado especificado en la ruta, entonces esa parte se eliminará de la URL. Un ejemplo de ello es el `(/<id>)` parte de la ruta por defecto, lo que no se incluirá en el url generada si un identificador no está previsto.

Un método que podría utilizar con frecuencia es el atajo [Request::uri] que es el mismo que el anterior excepto que asume la ruta actual, el directorio, el controlador y la acción. Si nuestra ruta actual es la predeterminada y la URL es `users/list`, podemos hacer lo siguiente para generar urls en el formato de `users/view/$id`:

    $this->request->uri(array('action' => 'view', 'id' => $user_id));
    
O si en un punto de vista, el método preferible es la siguiente:

    Request::instance()->uri(array('action' => 'view', 'id' => $user_id));

## Enlaces

[!!] links stub

