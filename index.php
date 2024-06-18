<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faixa da Sorte</title>
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Áudios -->
    <audio id="drumroll-sound" src="tambor.mp3"></audio>
    <audio id="plim-sound" src="Plim.mp3"></audio>
    <!-- CSS -->
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }
        .header {
            background-color: #263d76;
            color: #fff;
            padding: 10px 0;
            text-align: center;
        }
        .footer {
            background-color: #263d76;
            padding: 20px 0;
            text-align: center;
            color: white;
            width: 100%;
            position: absolute;
            bottom: 0;
            left: 0;
        }
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        h1 {
            color: #263d76;
            margin-bottom: 20px;
        }
        .faixa-container {
            position: relative;
            text-align: center;
            margin-top: 50px;
            overflow: hidden;
            width: 100%;
            height: 100px;
        }

        .faixa {
            display: flex;
            position: absolute;
            height: 100%;
            animation: faixaAnim 7s linear infinite;
            left: 0;
        }

        .faixa span {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 120px;
            height: 100%;
            font-size: 24px;
            font-weight: bold;
            color: #FFFFFF;
            background-color: #263d76;
            border: 1px solid #263d76;
            box-sizing: border-box;
        }

        .faixa span:nth-child(even) {
            background-color: #FFFFFF;
            color: #263d76;
        }

        #indicator {
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            height: 100%;
            width: 6px;
            background-color: red;
            z-index: 1;
        }

        @keyframes faixaAnim {
            0% {
                transform: translateX(0%);
            }
            100% {
                transform: translateX(-100%);
            }
        }

        #start-btn, #reset-btn {
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            background-color: #263d76;
            color: #fff;
            border: none;
            border-radius: 5px;
        }

        #start-btn:hover, #reset-btn:hover {
            background-color: #1a2e5a;
        }
    </style>
</head>
<body>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.2/dist/confetti.browser.min.js"></script>
    <!-- Cabeçalho -->
    <header class="header">
        <div class="container">
            <img src="src/logo.png" alt="Logo LIV Logistica" style="max-width: 130px;">
        </div>
    </header>

    <!-- Conteúdo Principal -->
    <div class="container main-content">
        <h1 class="text-center">SHOW DE PRÊMIOS</h1>
        <div class="faixa-container">
            <div id="indicator"></div>
            <div class="faixa" id="faixa">
                <?php
                    // Criar uma lista de números de 1 a 100
                    $numbers = range(1, 100);
                    // Embaralhar a lista de números
                    shuffle($numbers);
                    // Exibir os números em spans
                    foreach ($numbers as $number):
                ?>
                    <span><?php echo $number; ?></span>
                <?php endforeach; ?>
            </div>
        </div>
        <button id="start-btn">Rodar</button>
        <button id="reset-btn">Reiniciar</button>
    </div>

    <!-- Rodapé -->
    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?>  - Todos os direitos reservados - Túlio Silva.</p>
        </div>
    </footer>

    <script>
    const faixa = document.getElementById('faixa');
    const startBtn = document.getElementById('start-btn');
    const resetBtn = document.getElementById('reset-btn');
    const indicator = document.getElementById('indicator');
    const drumrollSound = document.getElementById('drumroll-sound');
    const plimSound = document.getElementById('plim-sound');
    let isStopped = true;
    let drawnNumbers = [];
    let listName = "default4";

    function getDrawnNumbers() {
        return JSON.parse(localStorage.getItem(`drawnNumbers_${listName}`)) || [];
    }

    function setDrawnNumbers(numbers) {
        localStorage.setItem(`drawnNumbers_${listName}`, JSON.stringify(numbers));
    }

    drawnNumbers = getDrawnNumbers();

    startBtn.addEventListener('click', () => {
        if (isStopped) {
            drumrollSound.currentTime = 0;
            drumrollSound.play();

            faixa.style.animationPlayState = 'running';
            isStopped = false;

            const stopAnimation = () => {
                faixa.style.animationPlayState = 'paused';
                isStopped = true;

                drumrollSound.pause();

                const indicatorCenter = indicator.getBoundingClientRect().left + indicator.offsetWidth / 2;
                const children = Array.from(faixa.children);

                let winningNumber = null;
                children.forEach(child => {
                    const childRect = child.getBoundingClientRect();
                    if (childRect.left <= indicatorCenter && childRect.right >= indicatorCenter) {
                        winningNumber = child.innerText;
                    }
                });

                if (winningNumber && !drawnNumbers.includes(winningNumber)) {
                    drawnNumbers.push(winningNumber);
                    setDrawnNumbers(drawnNumbers);
                    plimSound.currentTime = 0;
                    plimSound.play();

                    let params = {
                        particleCount: 1200,
                        spread: 90,
                        startVelocity: 70,
                        origin: { x: 0, y: 0.8 },
                        angle: 45
                    };

                    confetti(params);

                    params.origin.x = 1;
                    params.angle = 135;
                    confetti(params);
                } else {
                    // Se o número já foi sorteado, continuar a animação por mais 0.5 segundos
                    faixa.style.animationPlayState = 'running';
                    setTimeout(stopAnimation, 500);
                }
            };

            setTimeout(stopAnimation, 5000); // 5 seconds
        }
    });

    resetBtn.addEventListener('click', () => {
        location.reload(); // Recarrega a página para reiniciar o jogo
    });

    faixa.style.animationPlayState = 'paused';
    </script>
</body>
</html>
