<header>
    <button id="homeButton">Home</button>
    <h1>Solitar.io</h1>
    <div>
        <button id="profileButton"><img src="" alt="pfp"></button>
    </div>
    <script>
        document.getElementById('homeButton').onclick = function() {
            window.location.href = '/';
        };
        document.getElementById('profileButton').onclick = function() {
            window.location.href = '/profile';
        };
    </script>
</header>