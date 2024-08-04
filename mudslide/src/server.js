// server.js

const express = require('express');
const app = express();
const port = 3000;

// Middleware to parse request body
app.use(express.json());

app.get('/send/:message', (req, res) => {
    const message = req.params.message;

    // Logic to send the message to the mobile phone
    sendMessageToMobile(message);
    res.send(`Message "${message}" sent to mobile phone!`);
});

function sendMessageToMobile(message) {
    // Implement your logic to send the message to the mobile phone
    console.log(`Sending message to mobile phone: ${message}`);
}

app.listen(port, () => {
    console.log(`Node.js server listening at http://localhost:${port}`);
});

