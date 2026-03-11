import React, { useState, useEffect, useContext } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import api from '../api';
import { Container, Row, Col, Button, Badge, Spinner, Alert } from 'react-bootstrap';
import { AuthContext } from '../contexts/AuthContext';
import '../styles/Products.css';

const ProductDetail = () => {
    const { id } = useParams();
    const navigate = useNavigate();
    const { user } = useContext(AuthContext);
    const [product, setProduct] = useState(null);
    const [quantity, setQuantity] = useState(1);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');
    const [addingToCart, setAddingToCart] = useState(false);

    useEffect(() => {
        fetchProductDetail();
    }, [id]);

    const fetchProductDetail = async () => {
        setLoading(true);
        try {
            const res = await api.get(`/products/${id}`);
            setProduct(res.data);
        } catch (err) {
            setError('Failed to load product details');
        } finally {
            setLoading(false);
        }
    };

    const handleAddToCart = async () => {
        if (!user) {
            navigate('/login');
            return;
        }

        setAddingToCart(true);
        try {
            await api.post('/cart-items', {
                product_id: product.id,
                quantity: quantity
            });
            alert('Added to cart successfully!');
            setQuantity(1);
        } catch (err) {
            setError('Failed to add to cart');
        } finally {
            setAddingToCart(false);
        }
    };

    if (loading) {
        return (
            <div className="loading-container">
                <Spinner animation="border" role="status" className="spinner">
                    <span className="visually-hidden">Loading...</span>
                </Spinner>
                <p>Loading product details...</p>
            </div>
        );
    }

    if (!product) {
        return (
            <Container>
                <Alert variant="danger" className="mt-4">Product not found</Alert>
                <Button onClick={() => navigate('/products')} className="mt-3">Back to Products</Button>
            </Container>
        );
    }

    return (
        <div className="product-detail-container">
            <Container>
                <Button
                    onClick={() => navigate('/products')}
                    className="back-button mb-4"
                    variant="outline-secondary"
                >
                    ← Back to Products
                </Button>

                {error && <Alert variant="danger" className="mb-4">{error}</Alert>}

                <Row className="product-detail-row">
                    <Col lg={5} md={12} className="mb-4 mb-lg-0">
                        <div className="product-detail-image">
                            <div className="image-placeholder">📦</div>
                        </div>
                    </Col>

                    <Col lg={7} md={12}>
                        <div className="product-detail-content">
                            <div className="product-detail-header">
                                <h1 className="product-detail-title">{product.name}</h1>
                                <div className="rating-section">
                                    <span className="stars">⭐⭐⭐⭐⭐</span>
                                    <span className="rating-text">4.5 (120 reviews)</span>
                                </div>
                            </div>

                            <div className="pricing-section">
                                <span className="price-label">Price</span>
                                <div className="price-display">
                                    <span className="currency">$</span>
                                    <span className="amount">{product.price}</span>
                                </div>
                            </div>

                            <div className="description-section">
                                <h3 className="section-title">Description</h3>
                                <p className="product-description-text">
                                    {product.description || 'This is a premium quality product from our collection.'}
                                </p>
                            </div>

                            {product.categories && product.categories.length > 0 && (
                                <div className="categories-section">
                                    <h3 className="section-title">Categories</h3>
                                    <div className="categories-list">
                                        {product.categories.map((c) => (
                                            <Badge key={c.id} bg="info" className="category-badge">
                                                {c.name}
                                            </Badge>
                                        ))}
                                    </div>
                                </div>
                            )}

                            <div className="purchase-section">
                                <div className="quantity-section">
                                    <label className="quantity-label">Quantity:</label>
                                    <div className="quantity-controls">
                                        <button
                                            className="qty-btn"
                                            onClick={() => setQuantity(Math.max(1, quantity - 1))}
                                        >
                                            −
                                        </button>
                                        <input
                                            type="number"
                                            className="qty-input"
                                            value={quantity}
                                            onChange={(e) => setQuantity(Math.max(1, parseInt(e.target.value) || 1))}
                                            min="1"
                                        />
                                        <button
                                            className="qty-btn"
                                            onClick={() => setQuantity(quantity + 1)}
                                        >
                                            +
                                        </button>
                                    </div>
                                </div>

                                <Button
                                    className="add-to-cart-btn"
                                    onClick={handleAddToCart}
                                    disabled={addingToCart}
                                    size="lg"
                                >
                                    {addingToCart ? 'Adding to Cart...' : '🛒 Add to Cart'}
                                </Button>
                            </div>

                            <div className="info-boxes">
                                <div className="info-box">
                                    <span className="info-icon">🚚</span>
                                    <div>
                                        <strong>Free Shipping</strong>
                                        <p>On orders over $50</p>
                                    </div>
                                </div>
                                <div className="info-box">
                                    <span className="info-icon">🛡️</span>
                                    <div>
                                        <strong>Guaranteed Safe</strong>
                                        <p>30-day returns</p>
                                    </div>
                                </div>
                                <div className="info-box">
                                    <span className="info-icon">💳</span>
                                    <div>
                                        <strong>Secure Payment</strong>
                                        <p>SSL encrypted</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </Col>
                </Row>
            </Container>
        </div>
    );
};

export default ProductDetail;