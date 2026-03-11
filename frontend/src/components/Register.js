import React, { useState, useContext } from 'react';
import { Form, Button, Alert, Container, Row, Col } from 'react-bootstrap';
import { AuthContext } from '../contexts/AuthContext';
import { useNavigate, Link } from 'react-router-dom';
import '../styles/AuthForms.css';

const Register = () => {
    const { register } = useContext(AuthContext);
    const [name, setName] = useState('');
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);
    const navigate = useNavigate();

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        try {
            await register({ name, email, password });
            navigate('/');
        } catch (err) {
            setError('Registration failed. Please try again.');
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="auth-container">
            <Container>
                <Row className="justify-content-center align-items-center min-vh-100">
                    <Col md={5} sm={8} xs={12}>
                        <div className="auth-card">
                            <div className="auth-header">
                                <h1>Create Account</h1>
                                <p>Join us and start shopping</p>
                            </div>

                            {error && (
                                <Alert variant="danger" className="auth-alert" dismissible onClose={() => setError('')}>
                                    {error}
                                </Alert>
                            )}

                            <Form onSubmit={handleSubmit} className="auth-form">
                                <Form.Group className="form-group-custom">
                                    <Form.Label className="form-label-custom">Full Name</Form.Label>
                                    <Form.Control
                                        type="text"
                                        className="form-control-custom"
                                        placeholder="Enter your full name"
                                        value={name}
                                        onChange={(e) => setName(e.target.value)}
                                        required
                                        disabled={loading}
                                    />
                                </Form.Group>

                                <Form.Group className="form-group-custom">
                                    <Form.Label className="form-label-custom">Email Address</Form.Label>
                                    <Form.Control
                                        type="email"
                                        className="form-control-custom"
                                        placeholder="Enter your email"
                                        value={email}
                                        onChange={(e) => setEmail(e.target.value)}
                                        required
                                        disabled={loading}
                                    />
                                </Form.Group>

                                <Form.Group className="form-group-custom">
                                    <Form.Label className="form-label-custom">Password</Form.Label>
                                    <Form.Control
                                        type="password"
                                        className="form-control-custom"
                                        placeholder="Create a strong password"
                                        value={password}
                                        onChange={(e) => setPassword(e.target.value)}
                                        required
                                        disabled={loading}
                                    />
                                </Form.Group>

                                <Button 
                                    type="submit" 
                                    className="btn-login-custom"
                                    disabled={loading}
                                >
                                    {loading ? 'Creating Account...' : 'Create Account'}
                                </Button>
                            </Form>

                            <div className="auth-footer">
                                <p>
                                    Already have an account? 
                                    <Link to="/login" className="auth-link">Sign in</Link>
                                </p>
                            </div>
                        </div>
                    </Col>
                </Row>
            </Container>
        </div>
    );
};

export default Register;