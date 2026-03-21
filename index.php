<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Quiz System</title>
    <style>
        /* Reset some defaults */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: #fff;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            text-align: center;
            padding: 20px;
        }

        h1 {
            font-size: 3rem;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        p {
            font-size: 1.2rem;
            margin-bottom: 40px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
        }

        .button-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        a {
            padding: 15px 30px;
            background: #fff;
            color: #2575fc;
            font-weight: bold;
            text-decoration: none;
            border-radius: 50px;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        a:hover {
            transform: translateY(-5px);
            background: #f0f0f0;
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        }

        @media(max-width: 500px) {
            h1 {
                font-size: 2rem;
            }
            a {
                padding: 12px 25px;
            }
        }
    </style>
</head>
<body>

    <h1>Online Quiz System</h1>
    <p>Select where you want to go</p>

    <div class="button-container">
        <a href="admin/">Admin Panel</a>
        <a href="test.php">Test Page</a>
    </div>

</body>
</html>