export const renderName = (name, match) => {
    if (match.last_name) {
        if (match.first_name) {
            return `${name.last_name} ${name.first_name} ${name.father_name}`;
        }
        return `${name.last_name} ${name.first_name}`;
    }
    return name.last_name;
};

export const validateFullName = (fullName) => {
    const parts = fullName.trim().split(' ');
    const errors = [];

    if (parts.length < 1 || parts[0].length === 0 || parts[0].length < 3) {
        errors.push('Фамилию (не менее 3 символов)');
    }
    if (parts.length < 2 || parts[1].length === 0 || parts[1].length < 3) {
        errors.push('Имя (не менее 3 символов)');
    }
    if (parts.length < 3 || parts[2].length === 0 || parts[2].length < 3) {
        errors.push('Отчество (не менее 3 символов)');
    }

    return errors;
};


