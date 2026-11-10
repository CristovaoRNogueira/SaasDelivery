$(document).ready(function () {
	$('#telefone').mask('(00) 00000-0000');
	$('#whatsapp').mask('(00) 00000-0000');
	$('#cpf').mask('000.000.000-00');
	$('#cep').mask('00000-000');
	$('#cnpj').mask('00.000.000/0000-00');
	$('#data').mask('00/00/0000');

	$('#telefone_perfil').mask('(00) 00000-0000');
	$('#cpf_perfil').mask('000.000.000-00');
	$('#telefone_sistema').mask('(00) 00000-0000');
	$('#cnpj_sistema').mask('00.000.000/0000-00');
	$('#cep_perfil').mask('00000-000');

	$('#telefone2').mask('(00) 00000-0000');

	$('#telefone_fixo').mask('(00) 0000-0000');
});


function verificarTelefone(tel, valor) {

	if (valor.length > 14) {
		$('#' + tel).mask('(00) 00000-0000');
	} else if (valor.length == 14) {
		$('#' + tel).mask('(00) 0000-00000');
	} else {
		$('#' + tel).mask('(00) 0000-0000');

	}
}