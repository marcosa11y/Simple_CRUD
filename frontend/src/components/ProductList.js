import React, { useState, useEffect } from 'react';
import api from '../api';
import { Card, Button, Row, Col, Form } from 'react-bootstrap';
import { Link } from 'react-router-dom';

const ProductList = () => {
    const [products, setProducts] = useState([]);
    const [search, setSearch] = useState('');

    useEffect(() => {
        fetchProducts();
    }, []);

    const fetchProducts = async (term) => {
        const query = term ? `?search=${term}` : '';
        const res = await api.get(`/products${query}`);
        setProducts(res.data);
    };

    const handleSearch = (e) => {
        e.preventDefault();
        fetchProducts(search);
    };

    return (
        <div>
            <h2>Products</h2>
            <Form className="mb-3" onSubmit={handleSearch} inline>
                <Form.Control
                    type="text"
                    placeholder="Search"
                    value={search}
                    onChange={(e) => setSearch(e.target.value)}
                    className="me-2"
                />
                <Button type="submit">Search</Button>
            </Form>
            <Row>
                {products.map((p) => (
                    <Col key={p.id} sm={6} md={4} lg={3} className="mb-3">
                        <Card>
                            <Card.Body>
                                <Card.Title>{p.name}</Card.Title>
                                <Card.Text>${p.price}</Card.Text>
                                <Button as={Link} to={`/products/${p.id}`} variant="primary">
                                    View
                                </Button>
                            </Card.Body>
                        </Card>
                    </Col>
                ))}
            </Row>
        </div>
    );
};

export default ProductList;