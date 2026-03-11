import React, { useState, useContext } from 'react';
import { Form, Button, Alert, Container, Row, Col } from 'react-bootstrap';
import { AuthContext } from '../contexts/AuthContext';
import { useNavigate, Link } from 'react-router-dom';
import '../styles/AuthForms.css';

const Login = () => {
    const { login } = useContext(AuthContext);
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);
    const navigate = useNavigate();

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        try {
            await login({ email, password });
            navigate('/');
        } catch (err) {
            setError('Login failed. Please check your credentials.');
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
                                <h1>Welcome Back</h1>
                                <p>Sign in to your account</p>
                            </div>

                            {error && (
                                <Alert variant="danger" className="auth-alert" dismissible onClose={() => setError('')}>
                                    {error}
                                </Alert>
                            )}

                            <Form onSubmit={handleSubmit} className="auth-form">
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
                                        placeholder="Enter your password"
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
                                    {loading ? 'Signing in...' : 'Sign In'}
                                </Button>
                            </Form>

                            <div className="auth-footer">
                                <p>
                                    Don't have an account? 
                                    <Link to="/register" className="auth-link">Create one</Link>
                                </p>
                            </div>
                        </div>
                    </Col>
                </Row>
            </Container>
        </div>
    );
};

export default Login;