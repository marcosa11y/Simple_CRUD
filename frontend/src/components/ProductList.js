import React, { useState, useEffect } from 'react';
import api from '../api';
import { Card, Button, Row, Col, Form, Spinner, Container } from 'react-bootstrap';
import { Link } from 'react-router-dom';
import '../styles/Products.css';

const ProductList = () => {
    const [products, setProducts] = useState([]);
    const [search, setSearch] = useState('');
    const [loading, setLoading] = useState(true);
    const [filterPrice, setFilterPrice] = useState('all');

    useEffect(() => {
        fetchProducts();
    }, []);

    const fetchProducts = async (term) => {
        setLoading(true);
        try {
            const query = term ? `?search=${term}` : '';
            const res = await api.get(`/products${query}`);
            setProducts(res.data);
        } catch (err) {
            console.error('Error fetching products:', err);
        } finally {
            setLoading(false);
        }
    };

    const handleSearch = (e) => {
        e.preventDefault();
        fetchProducts(search);
    };

    const filteredProducts = filterPrice === 'all' ? products : products.filter(p => {
        if (filterPrice === 'under50') return p.price < 50;
        if (filterPrice === '50to100') return p.price >= 50 && p.price <= 100;
        if (filterPrice === 'over100') return p.price > 100;
        return true;
    });

    return (
        <div className="products-container">
            <Container>
                <div className="products-header">
                    <div>
                        <h1 className="products-title">Our Products</h1>
                        <p className="products-subtitle">Discover our premium collection</p>
                    </div>
                </div>

                <div className="products-search-section">
                    <Form onSubmit={handleSearch} className="search-form">
                        <Form.Group className="search-group">
                            <Form.Control
                                type="text"
                                placeholder="🔍 Search products..."
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                className="search-input"
                            />
                            <Button type="submit" className="search-btn">Search</Button>
                        </Form.Group>
                    </Form>

                    <div className="filter-section">
                        <label className="filter-label">Price Range:</label>
                        <select
                            value={filterPrice}
                            onChange={(e) => setFilterPrice(e.target.value)}
                            className="filter-select"
                        >
                            <option value="all">All Prices</option>
                            <option value="under50">Under $50</option>
                            <option value="50to100">$50 - $100</option>
                            <option value="over100">Over $100</option>
                        </select>
                    </div>
                </div>

                {loading ? (
                    <div className="loading-container">
                        <Spinner animation="border" role="status" className="spinner">
                            <span className="visually-hidden">Loading...</span>
                        </Spinner>
                        <p>Loading products...</p>
                    </div>
                ) : filteredProducts.length === 0 ? (
                    <div className="empty-state">
                        <h3>No products found</h3>
                        <p>Try searching with different keywords</p>
                    </div>
                ) : (
                    <>
                        <div className="products-count">
                            Showing {filteredProducts.length} product{filteredProducts.length !== 1 ? 's' : ''}
                        </div>
                        <Row className="products-grid">
                            {filteredProducts.map((p) => (
                                <Col key={p.id} sm={6} md={4} lg={3} className="mb-4">
                                    <div className="product-card-wrapper">
                                        <Card className="product-card">
                                            <div className="product-image-container">
                                                <div className="product-image-placeholder">
                                                    📦
                                                </div>
                                                {p.price && <div className="price-badge">${p.price}</div>}
                                            </div>
                                            <Card.Body className="product-body">
                                                <Card.Title className="product-name">{p.name}</Card.Title>
                                                <p className="product-description">
                                                    {p.description ? p.description.substring(0, 60) + '...' : 'Premium quality product'}
                                                </p>
                                                <div className="product-footer">
                                                    <div className="product-rating">⭐ 4.5 (120)</div>
                                                    <Button
                                                        as={Link}
                                                        to={`/products/${p.id}`}
                                                        className="view-btn"
                                                    >
                                                        View Details
                                                    </Button>
                                                </div>
                                            </Card.Body>
                                        </Card>
                                    </div>
                                </Col>
                            ))}
                        </Row>
                    </>
                )}
            </Container>
        </div>
    );
};

export default ProductList;