import React, { useState, useEffect } from 'react';
import api from '../api';
import { Container, Row, Col, Card, Spinner, Badge } from 'react-bootstrap';
import '../styles/Products.css';

const CategoryList = () => {
    const [categories, setCategories] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        fetchCategories();
    }, []);

    const fetchCategories = async () => {
        setLoading(true);
        try {
            const res = await api.get('/categories');
            setCategories(res.data);
        } catch (err) {
            console.error('Error fetching categories:', err);
        } finally {
            setLoading(false);
        }
    };

    const categoryIcons = {
        'Electronics': '💻',
        'Clothing': '👔',
        'Home': '🏠',
        'Sports': '⚽',
        'Books': '📚',
        'Food': '🍔',
        'Beauty': '💄',
        'Toys': '🧸'
    };

    const getIcon = (categoryName) => {
        return categoryIcons[categoryName] || '📦';
    };

    if (loading) {
        return (
            <div className="loading-container">
                <Spinner animation="border" role="status" className="spinner">
                    <span className="visually-hidden">Loading...</span>
                </Spinner>
                <p>Loading categories...</p>
            </div>
        );
    }

    return (
        <div className="categories-container">
            <Container>
                <div className="categories-header">
                    <h1 className="categories-title">Shop by Category</h1>
                    <p className="categories-subtitle">Browse our collection of premium products</p>
                </div>

                {categories.length === 0 ? (
                    <div className="empty-state">
                        <h3>No categories available</h3>
                        <p>Check back soon for new categories</p>
                    </div>
                ) : (
                    <Row className="categories-grid">
                        {categories.map((c) => (
                            <Col key={c.id} sm={6} md={4} lg={3} className="mb-4">
                                <Card className="category-card">
                                    <div className="category-icon-container">
                                        <span className="category-icon">{getIcon(c.name)}</span>
                                    </div>
                                    <Card.Body className="category-body">
                                        <Card.Title className="category-name">{c.name}</Card.Title>
                                        {c.products && c.products.length > 0 ? (
                                            <>
                                                <Badge bg="primary" className="product-count">
                                                    {c.products.length} product{c.products.length !== 1 ? 's' : ''}
                                                </Badge>
                                                <div className="products-preview mb-3">
                                                    <p className="preview-title">Featured items:</p>
                                                    <ul className="products-list">
                                                        {c.products.slice(0, 3).map((p) => (
                                                            <li key={p.id} className="product-item">
                                                                {p.name}
                                                            </li>
                                                        ))}
                                                        {c.products.length > 3 && (
                                                            <li className="more-items">
                                                                +{c.products.length - 3} more
                                                            </li>
                                                        )}
                                                    </ul>
                                                </div>
                                            </>
                                        ) : (
                                            <p className="no-products">No products yet</p>
                                        )}
                                    </Card.Body>
                                </Card>
                            </Col>
                        ))}
                    </Row>
                )}
            </Container>
        </div>
    );
};

export default CategoryList;