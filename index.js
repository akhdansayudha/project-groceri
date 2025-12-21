const express = require('express');
const app = express();
const PORT = 3000;

// Middleware
app.use(express.json());

// Routes
app.get('/', (req, res) => {
  res.send(`
    <h1>🛒 Groceri E-Commerce App</h1>
    <p>Backend API is running successfully in Docker!</p>
    <ul>
      <li><a href="/api/products">/api/products</a> - Get products</li>
      <li><a href="/health">/health</a> - Health check</li>
      <li><a href="/api/orders">/api/orders</a> - Orders endpoint</li>
    </ul>
  `);
});

// API Routes
app.get('/api/products', (req, res) => {
  res.json([
    { id: 1, name: 'Apple', price: 5000, stock: 100 },
    { id: 2, name: 'Banana', price: 3000, stock: 150 },
    { id: 3, name: 'Orange', price: 7000, stock: 80 }
  ]);
});

app.get('/api/orders', (req, res) => {
  res.json({ message: 'Orders endpoint ready' });
});

app.get('/health', (req, res) => {
  res.json({
    status: 'OK',
    message: 'Groceri app is running',
    environment: process.env.NODE_ENV || 'development',
    timestamp: new Date()
  });
});

// Error handling
app.use((req, res) => {
  res.status(404).json({ error: 'Route not found' });
});

// Start server
app.listen(PORT, '0.0.0.0', () => {
  console.log(`=================================`);
  console.log(`🛒 GROCERI APP STARTED`);
  console.log(`📡 Port: ${PORT}`);
  console.log(`📊 Health: http://localhost:${PORT}/health`);
  console.log(`🐳 Running in Docker Container`);
  console.log(`=================================`);
});
