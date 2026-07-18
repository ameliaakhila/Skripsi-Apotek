---
title: Apotek Stock Prediction API
emoji: 💊
colorFrom: blue
colorTo: green
sdk: docker
pinned: false
---

# Stock Prediction API
REST API for pharmacy stock prediction using Decision Tree Regressor.

## Endpoints
- `GET /status` - Health check
- `GET /model/info` - Model information
- `POST /predict` - Single prediction
- `POST /predict/batch` - Batch predictions
