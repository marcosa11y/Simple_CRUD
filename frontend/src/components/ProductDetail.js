import React, { useState, useEffect } from 'react';
import { useParams } from 'react-router-dom';
import api from '../api';
import { Card } from 'react-bootstrap';

const ProductDetail = () => {
    const { id } = useParams();
    const [product, setProduct] = useState(null);

    useEffect(() => {
        api.get(`/products/${id}`).then((res) => setProduct(res.data));
    }, [id]);

    if (!product) return <div>Loading...</div>;

    return (
        <Card>
            <Card.Body>
                <Card.Title>{product.name}</Card.Title>
                <Card.Text>{product.description}</Card.Text>
                <Card.Text>Price: ${product.price}</Card.Text>
                {product.categories && (
                    <Card.Text>
                        Categories: {product.categories.map((c) => c.name).join(', ')}
                    </Card.Text>
                )}
            </Card.Body>
        </Card>
    );
};

export default ProductDetail;