import React, { useContext } from 'react';
import { Link, NavLink, useNavigate } from 'react-router-dom';
import { Navbar, Nav, Container, Button } from 'react-bootstrap';
import { AuthContext } from '../contexts/AuthContext';

const NavBar = () => {
    const { user, logout, isAdmin } = useContext(AuthContext);
    const navigate = useNavigate();

    const handleLogout = () => {
        logout();
        navigate('/');
    };

    return (
        <Navbar bg="light" expand="lg">
            <Container>
                <Navbar.Brand as={Link} to="/">
                    E-commerce
                </Navbar.Brand>
                <Navbar.Toggle aria-controls="basic-navbar-nav" />
                <Navbar.Collapse id="basic-navbar-nav">
                    <Nav className="me-auto">
                        <Nav.Link as={NavLink} to="/products">
                            Products
                        </Nav.Link>
                        <Nav.Link as={NavLink} to="/categories">
                            Categories
                        </Nav.Link>
                        {user && (
                            <Nav.Link as={NavLink} to="/cart">
                                Cart
                            </Nav.Link>
                        )}
                        {user && isAdmin && (
                            <>
                                <Nav.Link as={NavLink} to="/admin/products">
                                    Manage Products
                                </Nav.Link>
                                <Nav.Link as={NavLink} to="/admin/categories">
                                    Manage Categories
                                </Nav.Link>
                            </>
                        )}
                    </Nav>
                    <Nav>
                        {user ? (
                            <>
                                <Navbar.Text className="me-2">Signed in as: {user.name}</Navbar.Text>
                                <Button variant="outline-secondary" size="sm" onClick={handleLogout}>
                                    Logout
                                </Button>
                            </>
                        ) : (
                            <>
                                <Nav.Link as={NavLink} to="/login">
                                    Login
                                </Nav.Link>
                                <Nav.Link as={NavLink} to="/register">
                                    Register
                                </Nav.Link>
                            </>
                        )}
                    </Nav>
                </Navbar.Collapse>
            </Container>
        </Navbar>
    );
};

export default NavBar;
