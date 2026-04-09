<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../PHPMailer/src/Exception.php';
require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';
require_once __DIR__ . '/../config/email.php';

function emailEsc(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}

function emailMoneyBr(float $valor): string
{
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

function emailTipoEntrega(string $tipoEntrega): string
{
    return $tipoEntrega === 'motoboy' ? 'Entrega por motoboy' : 'Retirada no local';
}

function gerarEmailPedidoHtml(array $pedido, array $itens, string $logoSrc = ''): string
{
    $linhas = '';

    foreach ($itens as $item) {
        $nome = emailEsc($item['nome']);
        $quantidade = (int)$item['quantidade'];
        $preco = emailMoneyBr((float)$item['preco']);
        $subtotal = emailMoneyBr((float)$item['subtotal']);

        $linhas .= '
            <tr>
                <td style="padding:14px 16px;border-bottom:1px solid #eadfcd;color:#2b2d2f;font-weight:700;">' . $nome . '</td>
                <td style="padding:14px 16px;border-bottom:1px solid #eadfcd;text-align:center;color:#5f6670;">' . $quantidade . '</td>
                <td style="padding:14px 16px;border-bottom:1px solid #eadfcd;text-align:right;color:#5f6670;">' . $preco . '</td>
                <td style="padding:14px 16px;border-bottom:1px solid #eadfcd;text-align:right;color:#8b5e3c;font-weight:800;">' . $subtotal . '</td>
            </tr>';
    }

    $logoHtml = $logoSrc !== ''
        ? '<img src="' . $logoSrc . '" alt="DoraNina" style="width:96px;display:block;margin:0 auto 16px;">'
        : '<div style="width:78px;height:78px;margin:0 auto 16px;border-radius:50%;background:#fff7ef;border:1px solid rgba(255,255,255,.35);display:grid;place-items:center;font:800 28px Arial,sans-serif;color:#8b5e3c;">DN</div>';

    $observacoes = trim((string)($pedido['observacoes'] ?? '')) !== ''
        ? nl2br(emailEsc((string)$pedido['observacoes']))
        : 'Sem observações.';

    $tipoEntrega = emailTipoEntrega((string)($pedido['tipo_entrega'] ?? 'retirada'));
    $endereco = (string)($pedido['tipo_entrega'] ?? '') === 'motoboy'
        ? emailEsc((string)$pedido['cliente_endereco'])
        : 'Retirada no local';

    return '<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Novo pedido DoraNina</title>
</head>
<body style="margin:0;padding:24px;background:#f5f1e8;font-family:Arial,sans-serif;color:#2b2d2f;">
    <div style="max-width:760px;margin:0 auto;">
        <div style="background:linear-gradient(135deg,#c68c53 0%,#8b5e3c 100%);border-radius:30px 30px 0 0;padding:34px 28px;text-align:center;color:#fff;box-shadow:0 18px 50px rgba(58,87,111,.12);">
            ' . $logoHtml . '
            <div style="font-weight:800;font-size:34px;line-height:1.1;">Novo pedido recebido</div>
            <div style="margin-top:10px;font-size:15px;opacity:.92;">Pedido #' . (int)$pedido['id'] . ' • DoraNina Bolos Artesanais</div>
        </div>

        <div style="background:#fdfaf4;border:1px solid rgba(139,94,60,.16);border-top:0;border-radius:0 0 30px 30px;padding:28px;box-shadow:0 18px 50px rgba(58,87,111,.12);">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:separate;border-spacing:0 14px;margin-bottom:12px;">
                <tr>
                    <td style="width:50%;vertical-align:top;">
                        <div style="background:#fffdf8;border:1px solid rgba(198,140,83,.18);border-radius:22px;padding:20px;">
                            <div style="font-weight:800;font-size:22px;color:#2b2d2f;margin-bottom:14px;">Cliente</div>
                            <div style="color:#5f6670;line-height:1.8;">
                                <strong style="color:#2b2d2f;">Nome:</strong> ' . emailEsc((string)$pedido['cliente_nome']) . '<br>
                                <strong style="color:#2b2d2f;">Telefone:</strong> ' . emailEsc((string)$pedido['cliente_telefone']) . '<br>
                                <strong style="color:#2b2d2f;">Endereço:</strong> ' . $endereco . '
                            </div>
                        </div>
                    </td>
                    <td style="width:14px;"></td>
                    <td style="width:50%;vertical-align:top;">
                        <div style="background:#fffdf8;border:1px solid rgba(198,140,83,.18);border-radius:22px;padding:20px;">
                            <div style="font-weight:800;font-size:22px;color:#2b2d2f;margin-bottom:14px;">Entrega</div>
                            <div style="color:#5f6670;line-height:1.8;">
                                <strong style="color:#2b2d2f;">Tipo:</strong> ' . emailEsc($tipoEntrega) . '<br>
                                <strong style="color:#2b2d2f;">Status:</strong> ' . emailEsc((string)$pedido['status']) . '<br>
                                <strong style="color:#2b2d2f;">Origem:</strong> Site DoraNina
                            </div>
                        </div>
                    </td>
                </tr>
            </table>

            <div style="background:#fffdf8;border:1px solid rgba(198,140,83,.18);border-radius:22px;padding:20px;margin-top:8px;">
                <div style="font-weight:800;font-size:22px;color:#2b2d2f;margin-bottom:16px;">Itens do pedido</div>
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;overflow:hidden;border-radius:18px;background:#ffffff;">
                    <thead>
                        <tr style="background:rgba(198,140,83,.12);">
                            <th style="padding:14px 16px;text-align:left;color:#8b5e3c;font-size:13px;letter-spacing:.02em;">Produto</th>
                            <th style="padding:14px 16px;text-align:center;color:#8b5e3c;font-size:13px;letter-spacing:.02em;">Qtd</th>
                            <th style="padding:14px 16px;text-align:right;color:#8b5e3c;font-size:13px;letter-spacing:.02em;">Unitário</th>
                            <th style="padding:14px 16px;text-align:right;color:#8b5e3c;font-size:13px;letter-spacing:.02em;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>' . $linhas . '</tbody>
                </table>
            </div>

            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-top:18px;border-collapse:separate;border-spacing:0 14px;">
                <tr>
                    <td style="vertical-align:top;">
                        <div style="background:#fffdf8;border:1px solid rgba(198,140,83,.18);border-radius:22px;padding:20px;">
                            <div style="font-weight:800;font-size:22px;color:#2b2d2f;margin-bottom:12px;">Observações</div>
                            <div style="color:#5f6670;line-height:1.7;">' . $observacoes . '</div>
                        </div>
                    </td>
                    <td style="width:14px;"></td>
                    <td style="width:230px;vertical-align:top;">
                        <div style="background:#8b5e3c;border-radius:22px;padding:22px;color:#fff;text-align:center;">
                            <div style="font-size:14px;opacity:.9;margin-bottom:8px;">Total do pedido</div>
                            <div style="font-weight:800;font-size:34px;line-height:1;">' . emailMoneyBr((float)$pedido['total']) . '</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>';
}

function enviarEmailPedido(array $pedido, array $itens): bool
{
    if (!app_email_is_configured()) {
        return false;
    }

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;
        $mail->CharSet = 'UTF-8';

        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress(EMAIL_PEDIDOS_DESTINO, EMAIL_PEDIDOS_NOME);
        $mail->addReplyTo(SMTP_FROM_EMAIL, SMTP_FROM_NAME);

        $logoPath = __DIR__ . '/../assets/img/logo-doranina.png';
        $logoSrc = '';
        if (is_file($logoPath)) {
            $mail->addEmbeddedImage($logoPath, 'logo_doranina', 'logo-doranina.png');
            $logoSrc = 'cid:logo_doranina';
        }

        $mail->isHTML(true);
        $mail->Subject = 'Novo pedido #' . (int)$pedido['id'] . ' - DoraNina';
        $mail->Body = gerarEmailPedidoHtml($pedido, $itens, $logoSrc);
        $mail->AltBody = "Novo pedido #" . (int)$pedido['id'] . " - DoraNina\n"
            . "Cliente: " . $pedido['cliente_nome'] . "\n"
            . "Telefone: " . $pedido['cliente_telefone'] . "\n"
            . "Tipo de entrega: " . emailTipoEntrega((string)$pedido['tipo_entrega']) . "\n"
            . "Total: " . emailMoneyBr((float)$pedido['total']);

        return $mail->send();
    } catch (Exception $e) {
        error_log('Erro ao enviar e-mail: ' . $mail->ErrorInfo);
        return false;
    }
}
