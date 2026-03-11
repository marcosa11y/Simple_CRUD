import React, { useState, useEffect } from 'react';
import api from '../api';
import { Button, ListGroup } from 'react-bootstrap';
import { Link } from 'react-router-dom';

const AdminCategoryList = () => {
    const [categories, setCategories] = useState([]);

    const load = async () => {
        const res = await api.get('/categories');
        setCategories(res.data);
    };

    useEffect(() => {
        load();
    }, []);

    const deleteCategory = async (id) => {
        if (!window.confirm('Delete this category?')) return;
        await api.delete(`/categories/${id}`);
        load();
    };

    return (
        <div>
            <h2>Manage Categories</h2>
            <Button as={Link} to="/admin/categories/new" className="mb-3">
                Add Category
            </Button>
            <ListGroup>
                {categories.map((c) => (
                    <ListGroup.Item key={c.id} className="d-flex justify-content-between align-items-center">
                        <span>{c.name}</span>
                        <div>
                            <Button
                                as={Link}
                                to={`/admin/categories/${c.id}/edit`}
                                size="sm"
                                className="me-2"
                            >
                                Edit
                            </Button>
                            <Button variant="danger" size="sm" onClick={() => deleteCategory(c.id)}>
                                Delete
                            </Button>
                        </div>
                    </ListGroup.Item>
                ))}
            </ListGroup>
        </div>
    );
};

export default AdminCategoryList;
