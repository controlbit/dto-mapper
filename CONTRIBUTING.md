# Contributing to DTO Mapper

Welcome and thank you for considering contributing to DTO Mapper!

All types of contributions are encouraged and valued. 
You will make some people smile and learn something new. That's for sure. 

## How to Contribute

### Reporting Bugs
If you find a bug, please create an issue. Include:
- A clear description of the bug.
- Steps to reproduce the issue (your source and destination objects/classes are welcome).
- Expected vs actual behavior - what you expected to happen and what actually happened, maybe it by design already.

### Feature Requests
We're always looking for ways to improve! If you have an idea for a new feature:
- Check if it's already in the [TODO list](README.md#todo-upcoming).
- Open an issue describing the feature and why it would be useful.

### Pull Requests
1. Fork the repository.
2. Create a new branch for your feature or bugfix.
3. Write tests for your changes.
4. Ensure all checks pass (see [Development](#development) below).
5. Submit a Pull Request with a clear description of your changes.

## Development

Would love to join the development team!
You found a bug and know how to fix it? 
Or you have an idea for a new feature, which you missed while using this bundle?
Let's make it happen!

### Setup
You can use the provided `Makefile` to set up your environment:
```bash
make setup
```
This will build the Docker container and install dependencies.

### Coding Standards
We use PHPStan and PHPMD to maintain code quality. You can run these checks using:
```bash
make code-check
```

### Testing
We use PHPUnit for testing and Infection for mutation testing. To run the full test suite (including infection):
```bash
make test
```

### Final Check
Before pushing your changes, it's recommended to run all checks:
```bash
make check
```

If you find that PHPMD is complaining about something out of your code scope, feel free to ignore it.

## Community
If you like the project, you can support it by:
- Starring the project on GitHub.
- Sharing it with your colleagues and friends.
- Referring to it in your own projects.