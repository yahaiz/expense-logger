# Contributing to ExpenseLogger

Thank you for your interest in contributing to ExpenseLogger! We welcome contributions from the community. This document provides guidelines and information for contributors.

## 🚀 Getting Started

### Prerequisites
- **PHP 8.2+** with SQLite extension
- **Node.js 18+** and npm
- **Git** for version control
- **Composer** for PHP dependencies

### Development Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/expenselogger.git
   cd expenselogger
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Set up environment**
   ```bash
   cp .env.example .env
   # Edit .env with your development settings
   ```

5. **Start development server**
   ```bash
   # For web development
   php -S localhost:8000

   # For Electron app development
   npm run dev
   ```

## 📋 Contribution Guidelines

### 🐛 Reporting Issues

When reporting bugs, please include:
- **Clear title** describing the issue
- **Steps to reproduce** the problem
- **Expected behavior** vs actual behavior
- **Environment details** (OS, PHP version, browser, etc.)
- **Screenshots** if applicable
- **Error logs** or console output

### 💡 Feature Requests

For new features, please:
- **Check existing issues** to avoid duplicates
- **Describe the problem** the feature would solve
- **Explain the solution** you'd like to see
- **Consider alternatives** you've thought about

### 🔧 Pull Requests

#### Before Submitting
- **Fork the repository** and create a feature branch
- **Write clear commit messages** following conventional commits
- **Test your changes** thoroughly
- **Update documentation** if needed
- **Ensure code style** consistency

#### PR Template
```markdown
## Description
Brief description of the changes made

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update
- [ ] Code refactoring

## Testing
- [ ] Unit tests added/updated
- [ ] Manual testing performed
- [ ] Cross-browser testing done

## Screenshots (if applicable)
Add screenshots of UI changes

## Checklist
- [ ] Code follows project style guidelines
- [ ] Tests pass locally
- [ ] Documentation updated
- [ ] No breaking changes
```

### 📝 Code Style

#### PHP
- Follow **PSR-12** coding standards
- Use meaningful variable and function names
- Add PHPDoc comments for classes and methods
- Use type hints where possible

#### JavaScript
- Use **ES6+** features
- Follow consistent naming conventions
- Add JSDoc comments for functions
- Use async/await for asynchronous operations

#### CSS
- Use TailwindCSS utility classes
- Follow BEM methodology for custom classes
- Maintain consistent spacing and naming

### 🧪 Testing

#### Running Tests
```bash
# PHP unit tests
composer test

# JavaScript tests (if applicable)
npm test
```

#### Writing Tests
- Write unit tests for new functions
- Include integration tests for API endpoints
- Test both success and error scenarios

### 📚 Documentation

#### Code Documentation
- Add PHPDoc/JSDoc comments to all public methods
- Update README.md for new features
- Document configuration options

#### User Documentation
- Update user guides for new features
- Add screenshots for UI changes
- Maintain clear installation instructions

## 🎯 Development Workflow

1. **Choose an issue** or create one
2. **Create a feature branch** (`git checkout -b feature/your-feature`)
3. **Make your changes** with clear commit messages
4. **Test thoroughly** - run tests and manual testing
5. **Update documentation** if needed
6. **Push your branch** and create a pull request
7. **Respond to feedback** and make requested changes
8. **Merge when approved**

## 🔒 Security

- **Never commit sensitive data** (passwords, API keys, etc.)
- **Report security vulnerabilities** privately
- **Follow secure coding practices**
- **Validate all inputs** and sanitize outputs

## 📞 Communication

- **Use GitHub Issues** for bugs and features
- **Use GitHub Discussions** for questions
- **Be respectful** and constructive in all communications
- **Help other contributors** when possible

## 🎉 Recognition

Contributors will be:
- Listed in the repository contributors
- Mentioned in release notes for significant contributions
- Recognized in the project's acknowledgments

Thank you for contributing to ExpenseLogger! 🎊