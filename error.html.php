* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.error-container {
    text-align: center;
    padding: 30px;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    width: 300px;
}

.error-code {
    font-size: 100px;
    color: #e74c3c;
    font-weight: bold;
}

.error-message {
    font-size: 18px;
    margin: 15px 0;
    color: #555;
}

.error-link {
    font-size: 16px;
    text-decoration: none;
    color: #3498db;
    border: 1px solid #3498db;
    padding: 8px 15px;
    border-radius: 5px;
    transition: background-color 0.3s;
}

.error-link:hover {
    background-color: #3498db;
    color: white;
}
