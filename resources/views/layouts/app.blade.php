<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <link rel="stylesheet" href="{{asset('css/app.css')}}">
</head>
<body>

    <nav class="navbar navbar-dark fixed-top">
        <a class="navbar-brand mx-auto" href="#"><img src="img/ufape.png" alt="" ></a>
        <a class="btn btn-outline-light btn-lg" onclick="enableMute()" target="_blank">Mutar</a>
    </nav>

    @yield('content')

    <audio id="background-sound" controls loop muted autoplay hidden>
        <source src="audio/world-of-warship.mp3" type="audio/mpeg">
    </audio>

    <script>
        var bgaudio = document.getElementById("background-sound");

        function enableMute() {
            if (bgaudio.muted){
                bgaudio.muted = false;
            } else {
                bgaudio.muted = true;
            }
        }
    </script>

</body>
</html>
