<?
$arCounters = [];

$arCounters['yandex-metrika'] = '(function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)}; m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)}) (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym"); ym(48394271, "init", { clickmap:true, trackLinks:true, accurateTrackBounce:true, webvisor:true, ecommerce:"dataLayer" }); ';

//$arCounters['vk-retargeting'] = '!function(){var t=document.createElement("script");t.async=!0,t.src="https://vk.com/js/api/openapi.js?169",t.onload=function(){VK.Retargeting.Init("VK-RTRG-1333576-3HV6Z"),VK.Retargeting.Hit()},document.head.appendChild(t)}();';

// $arCounters['facebook'] = "!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init', '1069296333256161');";

// $arCounters['google'] = "var gtm = document.createElement('script');gtm.setAttribute('src','https://www.googletagmanager.com/gtag/js?id=G-PXRT9JBT8X');document.head.appendChild(gtm); window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);}gtag('js', new Date()); gtag('config', 'G-PXRT9JBT8X');"

$arCounters['dolyame'] = "var digiScript = document.createElement('script'); digiScript.src = '//aq.dolyame.ru/5128/client.js?ts=' + Date.now(); digiScript.defer = true; digiScript.async = true; document.body.appendChild(digiScript);";


?>

<?/* global $APPLICATION;
if($APPLICATION->GetCurPage() != '/personal/order/success.php') {
    $arCounters['facebook-success-page'] = "fbq('track', 'PageView')";
}*/ ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
	setTimeout(function(){
        <?foreach($arCounters as $counter) {
            echo $counter . "\n\n";
        } ?>
	}, 4000); 
});
</script>