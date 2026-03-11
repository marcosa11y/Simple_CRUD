import React, { useState, useEffect } from 'react';
import api from '../api';
import { ListGroup } from 'react-bootstrap';

const CategoryList = () => {
    const [categories, setCategories] = useState([]);

    useEffect(() => {
        api.get('/categories').then((res) => setCategories(res.data));
    }, []);

    return (
        <div>
            <h2>Categories</h2>
            <ListGroup>
                {categories.map((c) => (
                    <ListGroup.Item key={c.id}>
                        {c.name}
                        {c.products && c.products.length > 0 && (
                            <div className="mt-1">
                                Products: {c.products.map((p) => p.name).join(', ')}
                            </div>
                        )}
                    </ListGroup.Item>
                ))}
            </ListGroup>
        </div>
    );
};

export default CategoryList;