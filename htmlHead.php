<!-- Head tag template to add a dynamic title and set scripts for create and edit files -->

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="/webdev2/project/">
    <link rel="icon" type="image/png" href="II.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <?php if($title === "Add Item" || $title === "Update Item."): ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sceditor@3/minified/themes/default.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sceditor@3/minified/sceditor.min.js"></script>
    <?php endif ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/luminous-lightbox/2.0.1/luminous-basic.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/luminous-lightbox/2.0.1/Luminous.min.js"></script>
    <script src="https://unpkg.com/react@18/umd/react.development.js"></script>
    <script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@google/generative-ai@latest/dist/generativeai.js"></script>
    <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>


    <link rel="stylesheet" href="styles/style.css">
    <title>Interiour Design Items - <?= $title ?></title>

</head>