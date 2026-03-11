import React, { useState, useEffect } from 'react';
import { Form, Button, Alert } from 'react-bootstrap';
import { useNavigate, useParams } from 'react-router-dom';
import api from '../api';

const CategoryForm = () => {
    const { id } = useParams();
    const [name, setName] = useState('');
    const [error, setError] = useState('');
    const navigate = useNavigate();

    useEffect(() => {
        if (id) {
            api.get(`/categories/${id}`).then((res) => {
                setName(res.data.name);
            });
        }
    }, [id]);

    const handleSubmit = async (e) => {
        e.preventDefault();
        try {
            if (id) {
                await api.put(`/categories/${id}`, { name });
            } else {
                await api.post('/categories', { name });
            }
            navigate('/admin/categories');
        } catch (err) {
            setError('Failed to save category');
        }
    };

    return (
        <div>
            <h2>{id ? 'Edit' : 'Add'} Category</h2>
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
                <Button type="submit">Save</Button>
            </Form>
        </div>
    );
};

export default CategoryForm;
