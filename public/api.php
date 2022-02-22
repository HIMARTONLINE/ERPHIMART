<?php

$id_product = $_REQUEST['id_product'];
$enlace_actual = 'https://' . $_SERVER['HTTP_HOST'] . '/erphimart/public/admin/productos/' .$id_product. '/edit';

$urlImage = 'https://himart.com.mx/api/images/products/' .$id_product. '/';
$key  = 'I24KTKXC8CLL94ENE1R1MX3SR8Q966H4';

//Here you set the path to the image you need to upload
$image_path = $_FILES['image']['name'];
$temp = $_FILES['image']['tmp_name'];

move_uploaded_file($temp, $image_path);

$args['image'] = new CurlFile($image_path);

$ch = curl_init();
curl_setopt($ch, CURLOPT_HEADER, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
curl_setopt($ch, CURLOPT_URL, $urlImage);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_USERPWD, $key.':');
curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

unlink($image_path);

header('Location: ' . $enlace_actual);

