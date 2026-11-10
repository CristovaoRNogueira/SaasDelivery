// Converter a data
function converterData(data) {

    // Converter a string em um objeto Date
    const dataObj = new Date(data);

    // Extrair o ano da data
    const ano = dataObj.getFullYear();

    // Obter o mês, mês começa de 0, padStart adiciona zeros à esquerda para garantir que o mês tenha dígitos
    const mes = String(dataObj.getMonth() + 1).padStart(2, '0');

    // Obter o dia do mês, padStart adiciona zeros à esquerda para garantir que o dia tenha dois dígitos
    const dia = String(dataObj.getDate()).padStart(2, '0');

    // Obter a hora, padStart adiciona zeros à esquerda para garantir que a hora tenha dois dígitos
    const hora = String(dataObj.getHours()).padStart(2, '0');

    // Obter minuto, padStart adiciona zeros à esquerda para garantir que o minuto tenha dois dígitos
    const minuto = String(dataObj.getMinutes()).padStart(2, '0');

    // Retornar a data
    return `${ano}-${mes}-${dia} ${hora}:${minuto}`;
}


// Converter a data selecionada para o formato necessário
function formatarDataParaDatetimeLocal(data) {
    const dataISO = new Date(data).toISOString();
    const dataFormatada = dataISO.slice(0, 16); // "YYYY-MM-DDTHH:MM"
    return dataFormatada;
}