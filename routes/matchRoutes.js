const express = require('express');
const fs = require('fs');
const path = require('path');
const natural = require('natural');
const stringSimilarity = require('string-similarity');

const router = express.Router();

router.post('/', (req, res) => {
  const { filename } = req.body;
  const uploadsDir = path.join(__dirname, '..', 'uploads');
  const targetFilePath = path.join(uploadsDir, filename);

  if (!fs.existsSync(targetFilePath)) {
    return res.status(404).json({ error: 'Target file not found.' });
  }

  const targetText = fs.readFileSync(targetFilePath, 'utf-8');

  const matches = [];

  fs.readdirSync(uploadsDir).forEach(file => {
    if (file === filename || path.extname(file) !== '.txt') return;

    const filePath = path.join(uploadsDir, file);
    const compareText = fs.readFileSync(filePath, 'utf-8');

    const cosine = stringSimilarity.compareTwoStrings(targetText, compareText);
    const levenshtein = natural.LevenshteinDistance(targetText, compareText);

    matches.push({
      file,
      cosine: cosine.toFixed(4),
      levenshtein
    });
  });

  matches.sort((a, b) => b.cosine - a.cosine);

  res.json({ matches });
});

module.exports = router;
