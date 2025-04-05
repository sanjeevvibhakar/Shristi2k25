const express = require('express');
const cors = require('cors');
const bodyParser = require('body-parser');

const uploadRoutes = require('./routes/uploadRoutes');
const matchRoutes = require('./routes/matchRoutes');

const app = express();

app.use(cors());
app.use(bodyParser.json());

// Serve uploaded files publicly
app.use('/uploads', express.static('uploads'));

app.use('/api/upload', uploadRoutes);
app.use('/api/match', matchRoutes);

app.listen(3000, () => {
  console.log('✅ Node server running on http://localhost:3000');
});
