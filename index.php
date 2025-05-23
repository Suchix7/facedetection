<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Face Attendance System</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <header>
            <h1>Face Attendance System</h1>
        </header>

        <main>
            <div class="attendance-section">
                <div class="video-container">
                    <video id="video" width="640" height="480" autoplay></video>
                    <canvas id="overlay" width="640" height="480" style="position: absolute; top: 0; left: 0;"></canvas>
                    <canvas id="canvas" width="640" height="480" style="display: none;"></canvas>
                </div>

                <div class="controls">
                    <button id="startButton" class="btn">Start Camera</button>
                    <button id="captureButton" class="btn" disabled>Capture & Mark Attendance</button>
                </div>

                <div class="status-container">
                    <div id="status" class="status">Status: Camera not started</div>
                    <div id="attendanceStatus" class="attendance-status"></div>
                </div>
            </div>

            <div class="attendance-list">
                <h2>Today's Attendance</h2>
                <div id="attendanceRecords"></div>
            </div>
        </main>
    </div>

    <script src="script.js"></script>
</body>

</html>