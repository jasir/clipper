# Clipper

Clipper is a small utility to take a text and clip regions marked with your own marks. Use it for example to clip only relevant parts from your html pages to generate pdf. **Marks can be nested**

    $text = <<<EOF
    <html>
    <!--start-->
    <body>
    <!--end-->
    <ul class="menu">
      <li> Wow
    </ul>
    <!--start-->
    <p>LoremIpsum</p>
    <!--end-->
    <footer>
    </footer>
    <!--start-->
    </body>
    <!--end-->
    </html>
    EOF;
    
## Clipping text between marks    
    
    $clipper = new Clipper('<!--start-->', '<!--end-->');
    $result = $cliper->clip($text)
    
    # $result contains <body><p>LoremIpsum</p>
    
## Clipping text not between marks    

    $result = $clipper->clipExclued($text);
    
    #result contains <html><body><ul class="menu"><li>Wow</ul><footer></footer></html>




