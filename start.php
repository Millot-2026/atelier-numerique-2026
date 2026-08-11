<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Centrale — Start</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: system-ui, -apple-system, sans-serif;
            background-color: #121212;
            color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            display: flex;
            flex-direction: row;
            gap: 3rem;
            width: 100%;
            max-width: 800px;
            padding: 2rem;
            justify-content: center;
            align-items: center;
        }
        .platform-card {
            background: #1e1e1e;
            border: 1px solid #333;
            border-radius: 12px;
            padding: 3rem 2rem;
            text-align: center;
            flex: 1;
            cursor: pointer;
            transition: transform 0.2s, border-color 0.2s;
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
        }
        .platform-card:hover {
            transform: translateY(-5px);
            border-color: #007acc;
        }
        .platform-card svg {
            width: 64px;
            height: 64px;
            fill: currentColor;
        }
        .platform-card span {
            font-size: 1.25rem;
            font-weight: 600;
        }
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                gap: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Windows -->
        <a href="index.php" class="platform-card" id="win-btn">
            <svg viewBox="0 0 88 88"><path d="M0 12.402l35.687-4.86v33.918H0V12.402zm35.687 35.688L0 53.224v31.78l35.687-4.918V48.09zM41.874 7.02L87.5 0v41.46H41.874V7.02zm45.626 36.63V88L41.874 81.63V43.65h45.626z"/></svg>
            <span>Windows</span>
        </a>
        <!-- Apple / Mac -->
        <a href="../Lancer.app" class="platform-card" id="mac-btn">
            <svg role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12.152 6.896c-.948 0-2.415-1.078-3.96-1.04-2.04.027-3.91 1.183-4.961 3.014-2.117 3.675-.546 9.103 1.519 12.09 1.013 1.454 2.208 3.09 3.792 3.039 1.52-.065 2.09-.987 3.935-.987 1.831 0 2.35.987 3.96.948 1.637-.026 2.676-1.48 3.676-2.948 1.156-1.688 1.636-3.325 1.662-3.415-.039-.013-3.182-1.221-3.22-4.857-.026-3.04 2.48-4.494 2.597-4.559-1.429-2.09-3.623-2.324-4.39-2.376-2-.156-3.675 1.09-4.61 1.09zM15.53 3.83c.843-1.012 1.4-2.427 1.245-3.83-1.207.052-2.662.805-3.532 1.818-.78.896-1.454 2.338-1.273 3.714 1.338.104 2.715-.688 3.559-1.701"/></svg>
            <span>macOS</span>
        </a>
    </div>
</body>
</html>