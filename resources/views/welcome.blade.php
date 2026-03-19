<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="orientacao">
        <h1>Importante!!!<br>De play no vídeo para maiores instruções de trabalho!!!</h1>
        <h2>↓↓↓↓↓</h2>
        <iframe 
        width="560" 
        height="315" 
        src="https://www.youtube.com/embed/AlVl7VM6wLU" 
        title="YouTube video player" 
        frameborder="0" 
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
        referrerpolicy="strict-origin-when-cross-origin" 
        allowfullscreen>
        </iframe>
    </div>
    <style>
        * {
            padding: 0;
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            box-sizing: border-box;
        }
        body {
            display: flex;
            min-height: 100vh;
            flex-direction: column;
        }
        .orientacao {
            display: flex;
            flex-direction: column;
            text-align: center;
            width: 100%;
            height: 100%;
            flex-grow: 1;
            justify-content: center;
            align-items: center;
            color: white;
            background-color: #333333;
            gap: 2rem;
        }
    </style>
</body>
</html>