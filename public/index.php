<?php 

require __DIR__.'/../vendor/autoload.php'; 

use Symfony\Component\HttpFoundation\Request; 

$app = new Silex\Application(); 

$app->register(new Silex\Provider\TwigServiceProvider(), [
  'twig.path' => __DIR__.'/../views',
]);

$app['debug'] = true; 

$app->get('/', function() use ($app) { 

  return $app['twig']->render('index.twig');

}); 

$app->post('/', function(Request $request) use ($app) { 

    // Grab the uploaded file
    $file = $request->files->get('upload'); 

    // Extract some information about the uploaded file
    $info = new SplFileInfo($file->getClientOriginalName());

    // Create a quasi-random filename
    $filename = sprintf('%d.%s', time(), $info->getExtension());

    // Copy the file
    $file->move(__DIR__.'/../uploads', $filename);

    // Instantiate the Tessearct library
    $tesseract = new TesseractOCR(__DIR__ . '/../uploads/' . $filename);

    // Perform OCR on the uploaded image
    $text = $tesseract->run();

    return $app['twig']->render(
    'results.twig',
    [
        'text'  =>  $text,
    ]
);

}); 

$app->run(); 