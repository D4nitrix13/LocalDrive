<!-- Autor: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Correo electrónico: danielperezdev@proton.me -->

# ***Diferencia entre CDN y cdnjs***

**1. CDN (Content Delivery Network):**

- *Es un término general que se refiere a una red de servidores distribuidos globalmente que se utilizan para entregar contenido, como Ficheros JavaScript, CSS, imágenes o videos, de manera rápida y eficiente.*
- *Ejemplos de servicios de CDN incluyen **jsDelivr**, **Cloudflare CDN**, **Google Hosted Libraries**, **AWS CloudFront**, entre otros.*
- *El propósito principal de un CDN es reducir la latencia, mejorar el tiempo de carga y proporcionar escalabilidad.*

**2. cdnjs:**

- *Es un servicio específico de CDN, mantenido por **Cloudflare**.*
- *Proporciona acceso a miles de bibliotecas JavaScript y CSS de código abierto.*
- *cdnjs es solo uno de los proveedores que utiliza la tecnología de CDN para distribuir bibliotecas populares.*

- **En tu ejemplo, estás utilizando dos servicios de CDN diferentes:**

1. **jsDelivr** *para cargar `bootstrap.bundle.min.js`:*

   ```html
   <script defer
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
      crossorigin="anonymous"></script>
   ```

   - **jsDelivr** *es un CDN popular que optimiza el rendimiento y permite acceder a recursos de NPM, GitHub y más.*

2. **cdnjs** *para cargar el tema "Darkly" de Bootswatch:*

   ```html
   <link data-theme="darkly" class="theme" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.3.3/darkly/bootstrap.min.css" integrity="sha512-HDszXqSUU0om4Yj5dZOUNmtwXGWDa5ppESlX98yzbBS+z+3HQ8a/7kcdI1dv+jKq+1V5b01eYurE7+yFjw6Rdg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
   ```

   - **cdnjs** *es otro servicio CDN ofrecido por Cloudflare que ofrece una amplia biblioteca de recursos, como temas y scripts.*

---

## **Foros**

- *[strlen](https://www.php.net/manual/en/function.strlen.php "https://www.php.net/manual/en/function.strlen.php")*
- *[substr](https://www.w3schools.com/php/php_string_slicing.asp "https://www.w3schools.com/php/php_string_slicing.asp")*
- *[How Do I Convert A String To A Number In PHP?](https://stackoverflow.com/questions/8529656/how-do-i-convert-a-string-to-a-number-in-php "https://stackoverflow.com/questions/8529656/how-do-i-convert-a-string-to-a-number-in-php")*
- *[Converting An Integer To A String In PHP](https://stackoverflow.com/questions/1035634/converting-an-integer-to-a-string-in-php "https://stackoverflow.com/questions/1035634/converting-an-integer-to-a-string-in-php")*
- *[Connecting to PostgreSQL](https://www.phptutorial.net/php-pdo/pdo-connecting-to-postgresql/ "https://www.phptutorial.net/php-pdo/pdo-connecting-to-postgresql/")*
- *[password_hash](https://www.php.net/manual/en/function.password-hash.php "https://www.php.net/manual/en/function.password-hash.php")*
- *[session_start](https://www.php.net/manual/en/function.session-start.php "https://www.php.net/manual/en/function.session-start.php")*
- *[PDOStatement::rowCount](https://www.php.net/manual/en/pdostatement.rowcount.php "https://www.php.net/manual/en/pdostatement.rowcount.php")*
- *[PDOStatement::fetch](https://www.php.net/manual/en/pdostatement.fetch.php "https://www.php.net/manual/en/pdostatement.fetch.php")*
- *[password_verify](https://www.php.net/manual/en/function.password-verify.php "https://www.php.net/manual/en/function.password-verify.php")*
- *[in_array](https://www.php.net/manual/en/function.in-array.php "https://www.php.net/manual/en/function.in-array.php")*
- *[Alerts Bootstrap](https://getbootstrap.com/docs/5.3/components/alerts/ "https://getbootstrap.com/docs/5.3/components/alerts/")*
- *[Where do browsers save cookies and data?](https://askubuntu.com/questions/89214/where-do-browsers-save-cookies-and-data "https://askubuntu.com/questions/89214/where-do-browsers-save-cookies-and-data")*
- *[Create a folder if it doesn't already exist](https://stackoverflow.com/questions/2303372/create-a-folder-if-it-doesnt-already-exist "https://stackoverflow.com/questions/2303372/create-a-folder-if-it-doesnt-already-exist")*
- *[How do I check if a directory exists? "is_dir", "file_exists" or both?](https://stackoverflow.com/questions/5425891/how-do-i-check-if-a-directory-exists-is-dir-file-exists-or-both "https://stackoverflow.com/questions/5425891/how-do-i-check-if-a-directory-exists-is-dir-file-exists-or-both")*
- *[Php create a file if not exists](https://stackoverflow.com/questions/20580017/php-create-a-file-if-not-exists "https://stackoverflow.com/questions/20580017/php-create-a-file-if-not-exists2")*
- *[Get the files inside a directory](https://stackoverflow.com/questions/1086105/get-the-files-inside-a-directory "https://stackoverflow.com/questions/1086105/get-the-files-inside-a-directory")*
- *[Recurso Icon](https://icon-icons.com/ "https://icon-icons.com/")*
- *[How can I get a file's extension in PHP?](https://stackoverflow.com/questions/173868/how-can-i-get-a-files-extension-in-php "https://stackoverflow.com/questions/173868/how-can-i-get-a-files-extension-in-php")*
- *[How to create .php files using php?](https://stackoverflow.com/questions/6411656/how-to-create-php-files-using-php "https://stackoverflow.com/questions/6411656/how-to-create-php-files-using-php")*
- *[How to write into a file in PHP?](https://stackoverflow.com/questions/1768894/how-to-write-into-a-file-in-php "https://stackoverflow.com/questions/1768894/how-to-write-into-a-file-in-php")*
- *[PostgreSQL Data Types](https://www.postgresql.org/docs/current/datatype.html "https://www.postgresql.org/docs/current/datatype.html")*
- *[PHP filemtime() Function](https://www.w3schools.com/php/func_filesystem_filemtime.asp "https://www.w3schools.com/php/func_filesystem_filemtime.asp")*
- *[How to delete a file via PHP?](https://stackoverflow.com/questions/2371408/how-to-delete-a-file-via-php "https://stackoverflow.com/questions/2371408/how-to-delete-a-file-via-php")*
- *[pathinfo](https://www.php.net/manual/en/function.pathinfo.php "https://www.php.net/manual/en/function.pathinfo.php")*
- *[Download files from server php](https://stackoverflow.com/questions/12094080/download-files-from-server-php "https://stackoverflow.com/questions/12094080/download-files-from-server-php")*
- *[Delete directory with files in it?](https://stackoverflow.com/questions/3349753/delete-directory-with-files-in-it "https://stackoverflow.com/questions/3349753/delete-directory-with-files-in-it")*
- *[escapeshellarg](https://www.php.net/manual/en/function.escapeshellarg.php "https://www.php.net/manual/en/function.escapeshellarg.php")*
- *[How do I bind a bigint value in PHP using PDO?](https://stackoverflow.com/questions/45716623/how-do-i-bind-a-bigint-value-in-php-using-pdo "https://stackoverflow.com/questions/45716623/how-do-i-bind-a-bigint-value-in-php-using-pdo")*
- *[Error calling procedure in posgres "No procedure matches the given name and argument types. You might need to add explicit type casts."](https://stackoverflow.com/questions/74207350/error-calling-procedure-in-posgres-no-procedure-matches-the-given-name-and-argu "https://stackoverflow.com/questions/74207350/error-calling-procedure-in-posgres-no-procedure-matches-the-given-name-and-argu")*
- *[Renaming files when downloading it](https://stackoverflow.com/questions/1801076/renaming-files-when-downloading-it "https://stackoverflow.com/questions/1801076/renaming-files-when-downloading-it2")*
- *[unlink](https://www.php.net/manual/en/function.unlink.php "https://www.php.net/manual/en/function.unlink.php")*
- *[How to list directories, sub-directories and all files in php](https://stackoverflow.com/questions/33188758/how-to-list-directories-sub-directories-and-all-files-in-php "https://stackoverflow.com/questions/33188758/how-to-list-directories-sub-directories-and-all-files-in-php")*
- *[Get URL query string parameters](https://stackoverflow.com/questions/8469767/get-url-query-string-parameters "https://stackoverflow.com/questions/8469767/get-url-query-string-parameters")*
- *[Get parent directory of running script](https://stackoverflow.com/questions/1882044/get-parent-directory-of-running-script "https://stackoverflow.com/questions/1882044/get-parent-directory-of-running-script2")*
- *[Get Folder or Directory size in PHP?](https://www.a2zwebhelp.com/folder-size-php "https://www.a2zwebhelp.com/folder-size-php")*
- *[How to get directory size in PHP](https://stackoverflow.com/questions/478121/how-to-get-directory-size-in-php "https://stackoverflow.com/questions/478121/how-to-get-directory-size-in-php")*
- *[PHP and PostgreSQL Transactions?](https://stackoverflow.com/questions/4405342/php-and-postgresql-transactions "https://stackoverflow.com/questions/4405342/php-and-postgresql-transactions")*
- *[PHP + MySQL transactions examples](https://stackoverflow.com/questions/2708237/php-mysql-transactions-examples "https://stackoverflow.com/questions/2708237/php-mysql-transactions-examples")*
- *[List all the files and folders in a Directory with PHP recursive function](https://stackoverflow.com/questions/24783862/list-all-the-files-and-folders-in-a-directory-with-php-recursive-function "https://stackoverflow.com/questions/24783862/list-all-the-files-and-folders-in-a-directory-with-php-recursive-function")*
- *[The RecursiveIteratorIterator class](https://www.php.net/manual/en/class.recursiveiteratoriterator.php "https://www.php.net/manual/en/class.recursiveiteratoriterator.php")*
- *[addslashes](https://www.php.net/manual/en/function.addslashes.php "https://www.php.net/manual/en/function.addslashes.php")*
- *[Posting form data with Ajax but post data gets truncated at server](https://stackoverflow.com/questions/14588008/posting-form-data-with-ajax-but-post-data-gets-truncated-at-server#29799476 "https://stackoverflow.com/questions/14588008/posting-form-data-with-ajax-but-post-data-gets-truncated-at-server")*
- *[PostgreSql 'PDOException' with message 'could not find driver'](https://stackoverflow.com/questions/10085039/postgresql-pdoexception-with-message-could-not-find-driver "https://stackoverflow.com/questions/10085039/postgresql-pdoexception-with-message-could-not-find-driver")*
- *[Description of core php.ini directives](https://www.php.net/manual/es/ini.core.php "https://www.php.net/manual/es/ini.core.php")*
- *[How do I create a 1GB random file in Linux?](https://superuser.com/questions/470949/how-do-i-create-a-1gb-random-file-in-linux "https://superuser.com/questions/470949/how-do-i-create-a-1gb-random-file-in-linux")*
- *[Gettype](https://www.php.net/manual/en/function.gettype.php "https://www.php.net/manual/en/function.gettype.php")*
- *[Apache2 Config Variable Is Not Defined](https://serverfault.com/questions/558283/apache2-config-variable-is-not-defined "https://serverfault.com/questions/558283/apache2-config-variable-is-not-defined")*
- *[Enable Php Apache2](https://stackoverflow.com/questions/42654694/enable-php-apache2 "https://stackoverflow.com/questions/42654694/enable-php-apache2")*
- *[Apache Is Running A Threaded Mpm, But Your Php Module Is Not Compiled To Be Threadsafe. You Need To Recompile Php. Ah00013: Pre-Configuration Failed](https://stackoverflow.com/questions/60370020/apache-is-running-a-threaded-mpm-but-your-php-module-is-not-compiled-to-be-thre "https://stackoverflow.com/questions/60370020/apache-is-running-a-threaded-mpm-but-your-php-module-is-not-compiled-to-be-thre")*

```php
// Para ver las propiedades del objeto
var_dump(get_object_vars($object));

// obtener los métodos de la clase de ese objeto. 
// $statement es un objeto de tipo PDOStatement, y esta función devolverá un array de métodos de esa clase.
var_dump(get_class_methods($statement));
```
