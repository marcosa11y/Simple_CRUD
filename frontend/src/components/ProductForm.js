import React, { useState, useEffect } from 'react';
import { Form, Button, Alert } from 'react-bootstrap';
import { useNavigate, useParams } from 'react-router-dom';
import api from '../api';

const ProductForm = () => {
    const { id } = useParams();
    const [name, setName] = useState('');
    const [description, setDescription] = useState('');
    const [price, setPrice] = useState('');
    const [stock, setStock] = useState('');
    const [categories, setCategories] = useState([]);
    const [selectedCats, setSelectedCats] = useState([]);
    const [image, setImage] = useState(null);
    const [error, setError] = useState('');
    const navigate = useNavigate();

    useEffect(() => {
        api.get('/categories').then((res) => setCategories(res.data));
        if (id) {
            api.get(`/products/${id}`).then((res) => {
                const p = res.data;
                setName(p.name);
                setDescription(p.description || '');
                setPrice(p.price);
                setStock(p.stock);
                setSelectedCats(p.categories ? p.categories.map((c) => c.id) : []);
            });
        }
    }, [id]);

    const handleSubmit = async (e) => {
        e.preventDefault();
        try {
            const formData = new FormData();
            formData.append('name', name);
            formData.append('description', description);
            formData.append('price', price);
            formData.append('stock', stock);
            selectedCats.forEach((c) => formData.append('categories[]', c));
            if (image) formData.append('image', image);

            if (id) {
                await api.post(`/products/${id}?_method=PUT`, formData, {
                    headers: { 'Content-Type': 'multipart/form-data' },
                });
            } else {
                await api.post('/products', formData, {
                    headers: { 'Content-Type': 'multipart/form-data' },
                });
            }
            navigate('/admin/products');
        } catch (err) {
            setError('Failed to save product');
        }
    };

    return (
        <div>
            <h2>{id ? 'Edit' : 'Add'} Product</h2>
            {error && <Alert variant="danger">{error}</Alert>}
            <Form onSubmit={handleSubmit}>
                <Form.Group className="mb-3">
                    <Form.Label>Name</Form.Label>
                    <Form.Control
                        type="text"
                        value={name}
                        onChange={(e) => setName(e.target.value)}
                    />
                </Form.Group>
                <Form.Group className="mb-3">
                    <Form.Label>Description</Form.Label>
                    <Form.Control
                        as="textarea"
                        rows={3}
                        value={description}
                        onChange={(e) => setDescription(e.target.value)}
                    />
                </Form.Group>
                <Form.Group className="mb-3">
                    <Form.Label>Price</Form.Label>
                    <Form.Control
                        type="number"
                        step="0.01"
                        value={price}
                        onChange={(e) => setPrice(e.target.value)}
                    />
                </Form.Group>
                <Form.Group className="mb-3">
                    <Form.Label>Stock</Form.Label>
                    <Form.Control
                        type="number"
                        value={stock}
                        onChange={(e) => setStock(e.target.value)}
                    />
                </Form.Group>
                <Form.Group className="mb-3">
                    <Form.Label>Categories</Form.Label>
                    <Form.Control
                        as="select"
                        multiple
                        value={selectedCats}
                        onChange={(e) => {
                            const opts = Array.from(e.target.selectedOptions).map(
                                (o) => o.value
                            );
                            setSelectedCats(opts);
                        }}
                    >
                        {categories.map((c) => (
                            <option key={c.id} value={c.id}>
                                {c.name}
                            </option>
                        ))}
                    </Form.Control>
                </Form.Group>
                <Form.Group className="mb-3">
                    <Form.Label>Image</Form.Label>
                    <Form.Control
                        type="file"
                        onChange={(e) => setImage(e.target.files[0])}
                    />
                </Form.Group>
                <Button type="submit">Save</Button>
            </Form>
        </div>
    );
};

export default ProductForm;
