import React, { useState, useEffect } from 'react';
import api from '../api';
import { Button, Row, Col, Card, Form } from 'react-bootstrap';
import { Link } from 'react-router-dom';

const AdminProductList = () => {
    const [products, setProducts] = useState([]);
    const [search, setSearch] = useState('');

    const fetchProducts = async (term) => {
        const query = term ? `?search=${term}` : '';
        const res = await api.get(`/products${query}`);
        setProducts(res.data);
    };

    useEffect(() => {
        fetchProducts();
    }, []);

    const handleSearch = (e) => {
        e.preventDefault();
        fetchProducts(search);
    };

    const deleteProduct = async (id) => {
        if (!window.confirm('Are you sure you want to delete this product?')) return;
        await api.delete(`/products/${id}`);
        fetchProducts(search);
    };

    return (
        <div>
            <h2>Manage Products</h2>
            <Button as={Link} to="/admin/products/new" className="mb-3">
                Add Product
            </Button>
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
                                <Button
                                    as={Link}
                                    to={`/admin/products/${p.id}/edit`}
                                    variant="secondary"
                                    size="sm"
                                    className="me-2"
                                >
                                    Edit
                                </Button>
                                <Button
                                    variant="danger"
                                    size="sm"
                                    onClick={() => deleteProduct(p.id)}
                                >
                                    Delete
                                </Button>
                            </Card.Body>
                        </Card>
                    </Col>
                ))}
            </Row>
        </div>
    );
};

export default AdminProductList;
