<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <title>Persona : Projet-test </title>
    <style>
        :root { --primary: #2c3e50; --accent: #3498db; --bg: #f4f7f6; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); color: var(--primary); margin: 0; padding: 20px; }
        .persona-container { max-width: 1000px; margin: 0 auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); display: grid; grid-template-columns: 300px 1fr; }
        .sidebar { background: var(--primary); color: white; padding: 30px; text-align: center; }
        .photo-placeholder { width: 150px; height: 150px; background: #bdc3c7; border-radius: 50%; margin: 0 auto 20px; border: 5px solid rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 50px; overflow:hidden; }
        .content { padding: 40px; }
        .grid-info { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .card { background: #fff; border: 1px solid #eee; padding: 15px; border-radius: 8px; border-left: 5px solid var(--accent); }
        h1 { margin: 0; font-size: 24px; }
        h3 { color: var(--accent); margin-top: 0; }
    </style>
</head>
<body>
    <div class='persona-container'>
        <div class='sidebar'>
            <div class='photo-placeholder'><img src='./img/photo.png' style='width:150px; height:150px; border-radius:50%; object-fit:cover;'></div>
            <h1>Projet-test </h1>
            <p>📍 Appartement</p>
            <p><i>LOREM_TEXT</i></p>
        </div>
        <div class='content'>
            <div class='grid-info'>
                <div class='card'><h3>Psychologie</h3><p>Sociable</p></div>
                <div class='card'><h3>Objectifs</h3><p>En apprendre toujours plus sur le développement web et l'IA</p></div>
                <div class='card'><h3>Besoins</h3><p>LOREM_TEXT</p></div>
                <div class='card'><h3>Freins</h3><p>24 heurs dans une journée, ce n'est pas assez.</p></div>
            </div>
        </div>
    </div>
</body>
</html>