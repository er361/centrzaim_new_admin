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
    const fullNameTrimmed = fullName.trim();
    const parts = fullNameTrimmed.split(' ');
    const errors = [];
    
    // Check if the total length is at least 3 symbols
    if (fullNameTrimmed.length < 3) {
        errors.push('ФИО (не менее 3 символов)');
        return errors;
    }
    
    // If the total length is sufficient but parts are missing, still show detailed errors
    if (parts.length < 1 || parts[0].length === 0) {
        errors.push('Фамилию');
    }
    if (parts.length < 2 || parts[1].length === 0) {
        errors.push('Имя');
    }
    if (parts.length < 3 || parts[2].length === 0) {
        errors.push('Отчество');
    }

    return errors;
};


