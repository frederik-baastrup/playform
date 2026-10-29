<?php
/**
 * SEO-tekst og FAQ til underkategorierne (landingssiderne).
 * Kun generel købsrådgivning – ingen påstande om konkrete produkter (projektregel 2).
 * Seedes af playform_seed_category_texts() og kan derefter rettes under Produkter › Kategorier.
 *
 * @return array<string,array{intro?:string,seo:string,faq:array<int,array{0:string,1:string}>}>
 */

defined( 'ABSPATH' ) || exit;

return [

	// Løbebånd.
	'foldbart-lobebaand' => [
		'seo' => '<h2>Foldbart løbebånd – når pladsen er trang</h2><p>Et foldbart løbebånd kan klappes sammen efter brug, så det fylder mindre i stuen, på kontoret eller i soveværelset. Her finder du foldbare løbebånd fra flere danske webshops samlet ét sted.</p><h3>Sådan foldes de</h3><p>De fleste modeller vippes op, så løbefladen står lodret, og mange har hjul, så båndet kan trilles væk. Nogle helt flade modeller foldes på midten og kan skubbes ind under en sofa eller seng. Tjek målene i sammenklappet stand, hvis du har et bestemt sted, båndet skal stå.</p><h3>Hvad skal du være opmærksom på?</h3><p>Foldbare løbebånd er ofte lidt lettere og har en kortere løbeflade end faste modeller. Til gang og let løb er det sjældent et problem, men løber du hurtigt eller er høj, bør du kigge efter en længere løbeflade og en højere tophastighed. Se også på den maksimale brugervægt, og om der er en gasdæmper, der gør det nemt og sikkert at folde båndet.</p>',
		'faq' => [
			[ 'Er et foldbart løbebånd lige så godt som et fast?', 'Til gang, jogging og almindelig konditionstræning er et foldbart løbebånd et fint valg. Faste modeller er ofte mere stabile og har længere løbeflade, hvilket betyder mest ved hurtigt løb eller tunge brugere.' ],
			[ 'Hvor meget plads fylder et foldbart løbebånd?', 'Sammenklappet fylder de typisk en brøkdel af gulvpladsen, men de skal stadig have plads til at blive foldet ud. Tjek både de udfoldede og de sammenklappede mål i produktbeskrivelsen.' ],
			[ 'Kan jeg løbe på et foldbart løbebånd?', 'Ja, mange foldbare løbebånd kan bruges til løb. Kig på tophastigheden og løbefladens længde – jo hurtigere og længere skridt, jo større løbeflade har du brug for.' ],
		],
	],
	'gaabaand' => [
		'seo' => '<h2>Gåbånd – bevægelse i hverdagen</h2><p>Et gåbånd er et lavt, fladt løbebånd beregnet til gang i roligt tempo. Det er populært på hjemmekontoret under et hæve-sænkebord, men kan også bruges foran tv’et. Her finder du gåbånd fra flere danske webshops.</p><h3>Gåbånd eller løbebånd?</h3><p>Et gåbånd har som regel ingen høje håndtag, en lavere tophastighed og en mere kompakt løbeflade. Det gør det let at stille væk, men det er ikke bygget til løb. Vil du både gå og løbe, så kig efter en model med højere tophastighed og håndtag, der kan foldes op.</p><h3>Hvad skal du kigge efter?</h3><p>Til brug under et skrivebord er lav højde og et stille motorlyd vigtige. Mange gåbånd styres med fjernbetjening eller app. Se også på den maksimale brugervægt, og om løbefladen er bred nok til, at du kan gå naturligt uden at tænke over, hvor du sætter fødderne.</p>',
		'faq' => [
			[ 'Kan man løbe på et gåbånd?', 'De fleste gåbånd er beregnet til gang og har en lav tophastighed. Nogle modeller kan bruges til let jogging, men vil du løbe, er et egentligt løbebånd et bedre valg.' ],
			[ 'Kan et gåbånd stå under et skrivebord?', 'Ja, det er det mest almindelige formål. Brug et hæve-sænkebord, så bordet kan stilles i den rigtige højde, mens du går.' ],
			[ 'Larmer et gåbånd?', 'Gåbånd er generelt stille, fordi de kører langsomt. En gummimåtte under båndet dæmper vibrationer, især i lejlighed.' ],
		],
	],
	'lobebaand-med-incline' => [
		'seo' => '<h2>Løbebånd med incline – træn op ad bakke</h2><p>Incline betyder, at løbefladen kan hæves, så du går eller løber op ad bakke. Det øger pulsen og forbrændingen uden at du behøver løbe hurtigere. Her finder du løbebånd med incline fra flere danske webshops.</p><h3>Manuel eller automatisk incline?</h3><p>På nogle modeller indstiller du hældningen manuelt, før du går i gang. Andre har motoriseret incline, der kan ændres med et tryk under træningen og ofte bruges i de indbyggede programmer. Motoriseret incline er mere fleksibel, men findes typisk på de lidt større modeller.</p><h3>Hvorfor træne med incline?</h3><p>Gang op ad bakke belaster leddene mindre end hurtigt løb, men giver stadig hård træning til ben, baller og kredsløb. Det er derfor populært både til rolig konditionstræning og til at variere løbeturen. Se på, hvor mange procent båndet kan hælde, og om det passer til den træning, du har i tankerne.</p>',
		'faq' => [
			[ 'Hvad er incline på et løbebånd?', 'Incline er løbefladens hældning. Når den hæves, simulerer du at gå eller løbe op ad bakke, hvilket gør træningen hårdere uden højere fart.' ],
			[ 'Er gang med incline god træning?', 'Ja, gang op ad bakke giver høj puls og træner ben og baller, samtidig med at det er skånsomt for leddene sammenlignet med hurtigt løb.' ],
			[ 'Hvad er forskellen på manuel og automatisk incline?', 'Manuel incline indstilles, før du starter. Automatisk incline styres med en motor, så du kan ændre hældningen undervejs og bruge den i træningsprogrammer.' ],
		],
	],
	'lobebaand-til-tunge-brugere' => [
		'seo' => '<h2>Løbebånd til tunge brugere</h2><p>Vejer du meget, eller skal løbebåndet bruges af flere i husstanden, er en høj maksimal brugervægt vigtig. Her har vi samlet løbebånd, hvor butikken angiver en høj maksimal brugervægt.</p><h3>Hvorfor betyder brugervægten noget?</h3><p>Den maksimale brugervægt fortæller, hvor meget løbebåndet er bygget til at bære. Et bånd, der bruges tæt på grænsen, slides hurtigere på motor, bælte og stel. Vælg derfor en model med god margin i forhold til din egen vægt.</p><h3>Hvad skal du ellers kigge efter?</h3><p>En kraftig motor, et solidt stel og en god dæmpning i løbefladen gør båndet mere stabilt og behageligt. En bred løbeflade giver ekstra sikkerhed. Tjek også garantien, og om båndet kan stå fast et sted, da de kraftigste modeller ofte er tunge at flytte.</p>',
		'faq' => [
			[ 'Hvor meget margin bør der være på brugervægten?', 'En god tommelfingerregel er at vælge et løbebånd, hvor den maksimale brugervægt ligger et godt stykke over din egen vægt, så båndet ikke kører tæt på grænsen.' ],
			[ 'Er løbebånd til tunge brugere mere støjende?', 'Ikke nødvendigvis. Kraftige modeller har ofte et solidt stel og god dæmpning, som kan gøre dem roligere. Læg en gummimåtte under for at dæmpe vibrationer.' ],
			[ 'Kan et løbebånd til tunge brugere foldes?', 'Nogle kan, men de mest robuste modeller er ofte faste. Tjek produktbeskrivelsen, hvis du har brug for at kunne folde båndet.' ],
		],
	],

	// Motionscykler.
	'spinningcykel' => [
		'seo' => '<h2>Spinningcykel – hård intervaltræning hjemme</h2><p>En spinningcykel har et tungt svinghjul og en sportslig siddestilling, der minder om en racercykel. Den er bygget til intervaller og spinninghold derhjemme. Her finder du spinningcykler fra flere danske webshops.</p><h3>Svinghjul og modstand</h3><p>Svinghjulet giver den jævne, tunge fornemmelse, når du træder. Modstanden reguleres enten med en filtbremse, der presser mod hjulet, eller med magneter. Magnetisk modstand er stille og slides ikke, mens filtbremse er enkel og ofte billigere.</p><h3>Indstil cyklen til dig</h3><p>Det er vigtigt, at både sadel og styr kan justeres i højden og frem og tilbage, så du sidder rigtigt. Mange bruger klikpedaler eller pedaler med stropper for et bedre greb. Vil du træne efter apps eller hold, så kig efter Bluetooth og et display med watt eller kadence.</p>',
		'faq' => [
			[ 'Hvad er forskellen på en spinningcykel og en kondicykel?', 'En spinningcykel har en sportslig, foroverlænet stilling og et tungt svinghjul til hårde intervaller. En kondicykel har en mere oprejst stilling og er beregnet til roligere træning.' ],
			[ 'Er magnetisk modstand bedre end filtbremse?', 'Magnetisk modstand er stille og vedligeholdelsesfri. Filtbremse giver en direkte fornemmelse og er ofte billigere, men filten slides med tiden.' ],
			[ 'Kan jeg bruge en spinningcykel med træningsapps?', 'Mange nyere modeller har Bluetooth og kan forbindes til apps. Tjek produktbeskrivelsen for, hvilke apps og data cyklen understøtter.' ],
		],
	],
	'kondicykel' => [
		'seo' => '<h2>Kondicykel – rolig og stabil træning</h2><p>En kondicykel har en oprejst siddestilling og trinvis modstand, så du nemt kan træne kondition i dit eget tempo. Her finder du kondicykler fra flere danske webshops.</p><h3>Til hvem er en kondicykel god?</h3><p>Kondicyklen er nem at komme i gang med og skånsom for knæ og ankler. Den passer til rolig motion, til at holde formen ved lige og til dig, der vil træne mens du ser tv eller lytter til en podcast.</p><h3>Hvad skal du kigge efter?</h3><p>Se på, hvor mange modstandsniveauer cyklen har, og om sadlen er behagelig og kan justeres til din højde. Et display med tid, distance og puls gør det nemt at følge træningen. Har du begrænset plads, så tjek målene – mange kondicykler har transporthjul.</p>',
		'faq' => [
			[ 'Er en kondicykel god til vægttab?', 'Kondicykling forbrænder energi og er nem at gøre til en vane. Kombineret med kost og regelmæssig træning kan den være en god hjælp.' ],
			[ 'Fylder en kondicykel meget?', 'Kondicykler er generelt kompakte. Mange har hjul, så de kan flyttes, når de ikke bruges.' ],
			[ 'Larmer en kondicykel?', 'De fleste kondicykler med magnetisk modstand er stille og velegnede til lejligheder.' ],
		],
	],
	'liggecykel' => [
		'seo' => '<h2>Liggecykel – skånsom træning med ryglæn</h2><p>På en liggecykel sidder du tilbagelænet med ryglæn og benene strakt fremad. Stillingen aflaster ryg, nakke og led, og derfor er den populær hos begyndere, ældre og dig, der foretrækker en behagelig siddestilling. Her finder du liggecykler fra flere danske webshops.</p><h3>Fordele ved en liggecykel</h3><p>Ryglænet giver støtte, og den lave indstigning gør det let at sætte sig op og stå af igen. Du kan træne længe uden at blive øm i sædet, og mange oplever stillingen som mere stabil end på en almindelig motionscykel.</p><h3>Hvad skal du kigge efter?</h3><p>Tjek, at sædet kan justeres, så du når pedalerne med let bøjede knæ. Se på den maksimale brugervægt, modstandsniveauerne og displayet. Liggecykler er længere end andre motionscykler, så mål pladsen op, før du bestiller.</p>',
		'faq' => [
			[ 'Er en liggecykel god ved dårlig ryg?', 'Mange med ondt i ryggen oplever liggecyklen som behagelig, fordi ryglænet støtter. Er du i behandling, så spørg din læge eller fysioterapeut til råds.' ],
			[ 'Får man lige så god træning på en liggecykel?', 'Ja, du kan sagtens træne hårdt på en liggecykel ved at skrue op for modstanden og tempoet. Den træner især benene og konditionen.' ],
			[ 'Fylder en liggecykel meget?', 'Den er længere end en almindelig motionscykel, fordi du sidder tilbagelænet. Tjek målene i produktbeskrivelsen.' ],
		],
	],
	'airbike' => [
		'seo' => '<h2>Airbike – helkropstræning med luftmodstand</h2><p>En airbike har en stor blæser i stedet for et svinghjul. Jo hårdere du træder og skubber og trækker i håndtagene, jo større bliver modstanden. Det gør den ideel til korte, intense intervaller. Her finder du airbikes fra flere danske webshops.</p><h3>Hvorfor vælge en airbike?</h3><p>Fordi du bruger både arme og ben, får du en hård træning af hele kroppen på kort tid. Modstanden følger din indsats, så den passer både til begyndere og til meget trænede. Airbiken er derfor populær i crossfit og funktionel træning.</p><h3>Hvad skal du være opmærksom på?</h3><p>Blæseren larmer mere end en almindelig motionscykel og giver en del luft. Det er værd at tænke over i en lejlighed. Kig på stellets stabilitet, den maksimale brugervægt og om der er et display, der viser watt, kalorier og tid.</p>',
		'faq' => [
			[ 'Hvad er en airbike god til?', 'En airbike er god til korte, hårde intervaller og helkropstræning, fordi du bruger både arme og ben, og modstanden stiger med din indsats.' ],
			[ 'Larmer en airbike?', 'Ja, blæseren larmer mere end en almindelig motionscykel. Den er bedst egnet til et rum, hvor lyden ikke generer.' ],
			[ 'Kan begyndere bruge en airbike?', 'Ja, modstanden afhænger af, hvor hårdt du træder, så du kan starte roligt og øge intensiteten, efterhånden som du bliver stærkere.' ],
		],
	],

	// Romaskiner og crosstrainere.
	'romaskine' => [
		'seo' => '<h2>Romaskiner til hjemmet</h2><p>En romaskine træner både ben, ryg, mave og arme i én bevægelse og er samtidig skånsom for leddene. Her finder du romaskiner med vand-, luft- og magnetmodstand fra flere danske webshops.</p><h3>Hvilken modstand skal du vælge?</h3><p><strong>Luftmodstand</strong> stiger, jo hårdere du trækker, og er populær til intervaller. <strong>Vandmodstand</strong> giver en naturlig rofornemmelse og en blød lyd. <strong>Magnetisk modstand</strong> er den mest stille og passer godt til lejligheder. Nogle maskiner kombinerer flere typer.</p><h3>Plads og opbevaring</h3><p>Romaskiner er lange, så mål op, før du bestiller. Mange kan stilles lodret op ad væggen eller klappes sammen, når de ikke bruges. Se også på skinnens længde, hvis du er høj, og på den maksimale brugervægt.</p>',
		'faq' => [
			[ 'Hvad træner en romaskine?', 'En romaskine træner ben, baller, ryg, mave og arme i én bevægelse og giver samtidig god konditionstræning.' ],
			[ 'Hvilken romaskine larmer mindst?', 'Romaskiner med magnetisk modstand er typisk de mest stille. Vandromaskiner giver en blød skvulpende lyd, mens luftmodstand larmer mest.' ],
			[ 'Kan en romaskine stilles væk?', 'Mange modeller kan stilles lodret eller klappes sammen. Tjek produktbeskrivelsen, hvis opbevaring er vigtig for dig.' ],
		],
	],
	'romaskine-vand' => [
		'intro' => 'Vandromaskiner giver en naturlig rofornemmelse med en blød, skvulpende lyd. Se romaskiner med vandmodstand fra flere danske webshops.',
		'seo'   => '<h2>Romaskine med vand – tættest på at ro på vandet</h2><p>I en vandromaskine roterer et skovlhjul i en tank med vand. Jo hurtigere du trækker, jo større bliver modstanden – præcis som når du ror en båd. Her finder du romaskiner med vandmodstand fra flere danske webshops.</p><h3>Hvordan føles det?</h3><p>Mange beskriver vandmodstand som den mest naturlige rofornemmelse. Trækket er jævnt, og lyden af vandet er rolig. Modstanden justeres ofte ved at ændre mængden af vand i tanken eller via et ventilsystem.</p><h3>Vedligeholdelse og plads</h3><p>Vandet skal skiftes eller renses med jævne mellemrum, typisk med en rensetablet. Mange vandromaskiner er lavet af træ og ser pæne ud i stuen, og en del kan stilles lodret, når de ikke bruges. Tjek tankens størrelse og maskinens mål, før du bestiller.</p>',
		'faq'   => [
			[ 'Hvordan virker en vandromaskine?', 'Et skovlhjul roterer i en tank med vand. Modstanden stiger, jo hurtigere du trækker, så det føles som at ro på vandet.' ],
			[ 'Skal man skifte vandet i en vandromaskine?', 'Vandet skal holdes rent, typisk med rensetabletter med jævne mellemrum. Følg producentens anvisning.' ],
			[ 'Larmer en vandromaskine?', 'Den giver en blød, skvulpende lyd, som de fleste oplever som behagelig. Den er mere stille end luftmodstand, men ikke helt lydløs.' ],
		],
	],
	'romaskine-luftmodstand' => [
		'intro' => 'Romaskiner med luftmodstand, hvor modstanden stiger med din indsats – populære til intervaller og konditionstræning. Se udvalget fra flere danske webshops.',
		'seo'   => '<h2>Romaskine med luftmodstand – til hård træning</h2><p>En romaskine med luftmodstand har et vifte- eller blæserhjul. Jo hårdere du trækker, jo mere luft skal hjulet skubbe, og jo tungere bliver det. Det giver en modstand, der altid følger din indsats. Her finder du romaskiner med luftmodstand fra flere danske webshops.</p><h3>Hvorfor luftmodstand?</h3><p>Luftmodstand er populær i fitnesscentre og crossfit, fordi den passer til både rolige ture og eksplosive intervaller. Mange modeller har et spjæld, så du kan ændre, hvor meget luft der kommer ind, og dermed hvor tungt trækket føles.</p><h3>Hvad skal du være opmærksom på?</h3><p>Luftmodstand larmer mere end vand- og magnetmodstand, så tænk over, hvor maskinen skal stå. Se på skinnens længde, den maksimale brugervægt og om displayet viser tid, distance, tempo og watt.</p>',
		'faq'   => [
			[ 'Hvordan virker luftmodstand på en romaskine?', 'Et blæserhjul drejer, når du trækker. Jo hårdere du trækker, jo mere luft skal flyttes, og jo større bliver modstanden.' ],
			[ 'Larmer en romaskine med luftmodstand?', 'Ja, den larmer mere end vand- og magnetmodstand, fordi luften suser gennem hjulet.' ],
			[ 'Er luftmodstand god til begyndere?', 'Ja, modstanden følger din indsats, så du selv bestemmer, hvor hårdt det er. Start roligt og fokuser på teknikken.' ],
		],
	],
	'romaskine-magnetisk' => [
		'intro' => 'Magnetiske romaskiner er stille og nemme at regulere – et godt valg til lejligheden. Se udvalget fra flere danske webshops.',
		'seo'   => '<h2>Magnetisk romaskine – stille træning</h2><p>I en magnetisk romaskine skabes modstanden af magneter, der bremser et svinghjul uden at røre det. Det gør maskinen stille og næsten vedligeholdelsesfri. Her finder du magnetiske romaskiner fra flere danske webshops.</p><h3>Fordele ved magnetisk modstand</h3><p>Magnetisk modstand er den mest stille type, og derfor et godt valg, hvis du bor i lejlighed eller vil træne, mens andre sover. Modstanden indstilles i faste trin, ofte med et håndtag eller på displayet, så du hurtigt kan finde det rigtige niveau igen.</p><h3>Hvad skal du kigge efter?</h3><p>Se på antallet af modstandsniveauer, skinnens længde og den maksimale brugervægt. Mange magnetiske romaskiner er kompakte og kan klappes sammen. Har du lyst til at træne med apps, så tjek om maskinen har Bluetooth.</p>',
		'faq'   => [
			[ 'Er en magnetisk romaskine stille?', 'Ja, magnetisk modstand er den mest stille type, fordi magneterne bremser hjulet uden berøring.' ],
			[ 'Hvordan justeres modstanden?', 'Typisk i faste trin med et håndtag eller via displayet, så du hurtigt kan vælge det samme niveau igen.' ],
			[ 'Kræver en magnetisk romaskine vedligeholdelse?', 'Meget lidt. Hold skinnen og sædet rene, og følg producentens anvisninger.' ],
		],
	],
	'romaskine-til-hjemmetraening' => [
		'intro' => 'Romaskiner, som butikkerne beskriver til brug i hjemmet. Se udvalget fra flere danske webshops samlet ét sted.',
		'seo'   => '<h2>Romaskine til hjemmetræning</h2><p>En romaskine er et af de mest effektive stykker udstyr til hjemmetræning, fordi den træner næsten hele kroppen og samtidig er skånsom for leddene. Her har vi samlet romaskiner, som butikkerne selv beskriver til brug i hjemmet.</p><h3>Hvad betyder noget derhjemme?</h3><p>I hjemmet er plads og lyd ofte de vigtigste ting. Mange romaskiner kan stilles lodret eller klappes sammen, og magnetisk modstand er den mest stille. Vandromaskiner i træ er populære, fordi de ser pæne ud i stuen.</p><h3>Kom godt i gang</h3><p>Fokuser på teknikken: skub fra med benene først, læn dig let tilbage, og træk så håndtaget ind mod brystet. Start med korte ture i roligt tempo, og øg varigheden, efterhånden som du vænner dig til bevægelsen.</p>',
		'faq'   => [
			[ 'Hvilken romaskine er bedst til hjemmet?', 'Det afhænger af plads og lyd. Magnetiske romaskiner er de mest stille, vandromaskiner giver en naturlig fornemmelse, og mange modeller kan stilles væk efter brug.' ],
			[ 'Hvor meget plads skal der være til en romaskine?', 'Romaskiner er lange, så mål både længden og pladsen omkring maskinen. Tjek også målene, hvis den kan stilles lodret.' ],
			[ 'Hvor ofte skal jeg ro?', 'Start gerne med et par korte ture om ugen, og øg langsomt. Det vigtigste er, at du får en god teknik og træner fast.' ],
		],
	],
	'crosstrainer' => [
		'seo' => '<h2>Crosstrainere til hjemmet</h2><p>En crosstrainer kombinerer gang, løb og skiløb i én blød bevægelse, hvor fødderne aldrig forlader pedalerne. Det gør den skånsom for knæ og ankler, og du træner både arme og ben. Her finder du crosstrainere fra flere danske webshops.</p><h3>Skridtlængde og svinghjul</h3><p>Skridtlængden er afstanden, pedalerne bevæger sig. Jo højere du er, jo længere skridtlængde giver en naturlig bevægelse. Et tungt svinghjul giver en jævn og rolig gang i maskinen.</p><h3>Hvad skal du ellers kigge efter?</h3><p>Se på modstandsniveauerne, den maksimale brugervægt og om der er incline eller justerbare pedaler. Crosstrainere fylder en del, og de færreste kan klappes sammen, så mål pladsen op, før du bestiller.</p>',
		'faq' => [
			[ 'Er en crosstrainer god træning?', 'Ja, du træner både arme og ben og får god konditionstræning, samtidig med at bevægelsen er skånsom for leddene.' ],
			[ 'Hvilken skridtlængde skal jeg vælge?', 'Jo højere du er, jo længere skridtlængde bør du vælge for en naturlig bevægelse. Tjek producentens anbefaling.' ],
			[ 'Fylder en crosstrainer meget?', 'Ja, de fleste står fast og fylder en del gulvplads. Mål pladsen op, før du bestiller.' ],
		],
	],

	// Vægte og kettlebells.
	'kettlebell' => [
		'seo' => '<h2>Kettlebells – helkropstræning med én vægt</h2><p>En kettlebell er en kuglevægt med håndtag, der bruges til svingende og eksplosive øvelser som swings, cleans og goblet squats. Med én kettlebell kan du træne hele kroppen. Her finder du kettlebells fra flere danske webshops.</p><h3>Hvilken vægt skal du vælge?</h3><p>Som tommelfingerregel starter mange kvinder med 8–12 kg og mange mænd med 12–16 kg til grundøvelserne. Til swings kan du ofte bruge mere end til øvelser over hovedet. Har du plads, er to forskellige vægte en god start.</p><h3>Støbejern, vinyl eller konkurrence?</h3><p>Kettlebells i støbejern er holdbare og klassiske. Vinyl- eller gummibelagte skåner gulvet. Konkurrence-kettlebells har samme størrelse uanset vægt, så teknikken er den samme, når du skifter vægt. Kig også på håndtaget: det skal være glat og bredt nok til begge hænder.</p>',
		'faq' => [
			[ 'Hvilken kettlebell skal jeg starte med?', 'Mange kvinder starter med 8–12 kg og mange mænd med 12–16 kg. Vælg en vægt, du kan bruge med god teknik.' ],
			[ 'Hvad er en konkurrence-kettlebell?', 'Konkurrence-kettlebells har samme størrelse og håndtag uanset vægt, så du ikke skal ændre teknik, når du skifter til en tungere.' ],
			[ 'Hvad kan man træne med en kettlebell?', 'Hele kroppen – fx swings, squats, presses og rows. Kettlebellen er især god til hofter, ben, ryg og kondition.' ],
		],
	],
	'haandvaegte' => [
		'seo' => '<h2>Håndvægte til styrketræning hjemme</h2><p>Håndvægte er grundstenen i mange hjemmegyms. De kan bruges til øvelser for hele kroppen – fra biceps curls og skulderpres til lunges og rows. Her finder du håndvægte fra flere danske webshops.</p><h3>Faste eller justerbare?</h3><p>Faste håndvægte er klar til brug og hurtige at skifte mellem, men du skal bruge flere par for at variere vægten. Justerbare håndvægte dækker mange vægte i ét sæt og sparer plads, men tager lidt tid at stille om.</p><h3>Materiale og greb</h3><p>Hexagon-håndvægte med gummi ruller ikke væk og skåner gulvet. Støbejern er holdbart, og vinyl er et billigt valg til lettere vægte. Se på grebet – et rillet håndtag giver bedre fat, når du sveder.</p>',
		'faq' => [
			[ 'Hvilke håndvægte skal jeg starte med?', 'Start med en vægt, du kan løfte 10–15 gange med god teknik. Mange vælger et par lette og et par tungere, eller justerbare håndvægte.' ],
			[ 'Er justerbare håndvægte en god idé?', 'Ja, især hvis du har lidt plads. Ét sæt kan erstatte mange par faste håndvægte.' ],
			[ 'Hvad er hexagon-håndvægte?', 'Håndvægte med sekskantede ender. De ruller ikke væk og er populære, fordi de er stabile på gulvet.' ],
		],
	],
	'vaegtskiver' => [
		'seo' => '<h2>Vægtskiver til stang og håndvægte</h2><p>Vægtskiver bruges på vægtstænger og justerbare håndvægte, så du kan øge belastningen i takt med, at du bliver stærkere. Her finder du vægtskiver fra flere danske webshops.</p><h3>Lille eller stort hul?</h3><p>Vægtskiver findes med et lille hul til standardstænger og et stort, olympisk hul til de kraftigere stænger, der bruges i racks. Skiver og stang skal passe sammen, så tjek hullets størrelse, før du køber.</p><h3>Bumper plates eller støbejern?</h3><p>Bumper plates er gummiskiver, der kan tåle at blive tabt fra højde, fx ved markløft og olympiske løft. Støbejernsskiver er tyndere, så der kan være flere på stangen. Gummibelagte skiver larmer mindre og skåner gulvet.</p>',
		'faq' => [
			[ 'Passer alle vægtskiver til alle stænger?', 'Nej. Skiver med lille hul passer til standardstænger, og skiver med stort hul passer til olympiske stænger.' ],
			[ 'Hvad er bumper plates?', 'Gummiskiver, der kan tåle at blive tabt, og som har samme diameter uanset vægt. De bruges især til markløft og olympiske løft.' ],
			[ 'Hvilke vægtskiver larmer mindst?', 'Gummi- og gummibelagte skiver larmer mindre og skåner gulvet bedre end rene støbejernsskiver.' ],
		],
	],
	'vaegtstaenger' => [
		'seo' => '<h2>Vægtstænger til hjemmegymmet</h2><p>En vægtstang giver dig mulighed for de klassiske basisøvelser som squat, bænkpres og markløft, hvor du kan løfte tungt med begge hænder. Her finder du vægtstænger fra flere danske webshops.</p><h3>Olympisk eller standard?</h3><p>Olympiske stænger er kraftigere, har roterende ender og passer til skiver med stort hul. De er det mest brugte valg i racks. Standardstænger er lettere og billigere og passer til skiver med lille hul.</p><h3>Andre typer stænger</h3><p>En EZ-curlstang er bøjet, så håndleddene skånes ved bicepsøvelser. Korte stænger er lette at have med at gøre i små rum. Se på stangens maksimale belastning, længde og greb, før du vælger.</p>',
		'faq' => [
			[ 'Hvad er en olympisk vægtstang?', 'En kraftig stang med roterende ender, der passer til vægtskiver med stort hul. Den bruges typisk til tunge løft i et rack.' ],
			[ 'Hvad bruges en EZ-curlstang til?', 'Den bøjede form skåner håndleddene ved fx biceps curls og triceps-øvelser.' ],
			[ 'Hvor lang skal vægtstangen være?', 'Det afhænger af øvelserne og rummet. Til et rack passer en olympisk stang i fuld længde, mens kortere stænger er praktiske på lidt plads.' ],
		],
	],
	'vaegtstativ' => [
		'seo' => '<h2>Vægtstativ – orden i hjemmegymmet</h2><p>Et vægtstativ holder styr på håndvægte, kettlebells eller vægtskiver, så de ikke ligger på gulvet. Det giver orden og gør det hurtigere at finde den rigtige vægt. Her finder du vægtstativer fra flere danske webshops.</p><h3>Hvilket stativ passer til dig?</h3><p>Der findes stativer til håndvægte i flere etager, stativer med pinde til vægtskiver og hylder til kettlebells. Nogle kan rumme både håndvægte og skiver. Tænk på, hvor mange vægte du har nu, og hvor mange du forventer at få.</p><h3>Stabilitet og plads</h3><p>Se på stativets maksimale belastning og om det står stabilt, når det er fyldt. Et stativ med plads til at tage vægtene direkte fra hylden sparer ryggen.</p>',
		'faq' => [
			[ 'Hvorfor have et vægtstativ?', 'Det holder orden, beskytter gulvet og gør det let at finde den vægt, du skal bruge.' ],
			[ 'Kan et stativ rumme både håndvægte og skiver?', 'Nogle stativer kan. Tjek produktbeskrivelsen for, hvad stativet er beregnet til.' ],
			[ 'Hvad skal jeg kigge efter?', 'Maksimal belastning, stabilitet og om der er plads til dine vægte – også dem, du måske køber senere.' ],
		],
	],

	// Bænke og racks.
	'traeningsbaenk' => [
		'seo' => '<h2>Træningsbænke til hjemmegymmet</h2><p>En træningsbænk giver dig mange flere øvelser med håndvægte og vægtstang – fra bænkpres og rows til step-ups. Her finder du træningsbænke fra flere danske webshops.</p><h3>Flad, justerbar eller foldbar?</h3><p>En flad bænk er enkel og stabil. En justerbar bænk kan vinkles til skrå- og negativ bænkpres og giver flest muligheder. En foldbar bænk kan stilles væk, når den ikke bruges.</p><h3>Hvad skal du kigge efter?</h3><p>Se på den maksimale belastning, om bænken står stabilt, og om polstringen er fast. Skal bænken bruges i et rack, så tjek højden og bredden. Hjul og håndtag gør det nemt at flytte den.</p>',
		'faq' => [
			[ 'Hvilken træningsbænk skal jeg vælge?', 'En justerbar bænk giver flest øvelser. Har du lidt plads, kan en foldbar bænk være et godt valg.' ],
			[ 'Hvor meget skal en træningsbænk kunne bære?', 'Den skal kunne bære din egen vægt plus den vægt, du løfter. Vælg en bænk med god margin.' ],
			[ 'Kan en træningsbænk bruges i et rack?', 'Ja, de fleste kan. Tjek bænkens mål i forhold til rackets bredde.' ],
		],
	],
	'power-rack' => [
		'seo' => '<h2>Power rack – tunge løft med sikkerhed</h2><p>Et power rack er et stativ, der omslutter dig med justerbare sikkerhedsstænger. Det gør det sikkert at squatte og bænkpresse tungt, også når du træner alene. Her finder du power racks, half racks og squat stands fra flere danske webshops.</p><h3>Power rack, half rack eller squat stand?</h3><p>Et power rack har stolper på alle fire hjørner og giver mest sikkerhed. Et half rack er mere åbent og fylder mindre. En squat stand er den mest kompakte løsning, men giver mindre beskyttelse, hvis du ikke kan løfte vægten op igen.</p><h3>Plads og tilbehør</h3><p>Mål loftshøjden og gulvpladsen, før du vælger. Mange racks har pull-up-stang, dip-håndtag eller plads til vægtskiver, og nogle kan udvides med kabeltræk. Tjek også, om stangen og skiverne passer til racket.</p>',
		'faq' => [
			[ 'Hvorfor træne i et power rack?', 'Sikkerhedsstængerne tager vægten, hvis du ikke kan løfte den op igen. Det gør det sikkert at træne tungt alene.' ],
			[ 'Hvor meget loftshøjde kræver et power rack?', 'Det varierer efter model. Tjek rackets højde og husk plads til pull-ups, hvis det har en stang.' ],
			[ 'Hvad er forskellen på et power rack og et half rack?', 'Et power rack har stolper på alle sider og giver mest sikkerhed. Et half rack er mere åbent og fylder mindre.' ],
		],
	],
	'multigym' => [
		'seo' => '<h2>Multigym – mange øvelser i én maskine</h2><p>En multigym samler mange styrkeøvelser i én maskine med kabeltræk og en vægtstak. Du skifter vægt ved at flytte en pind, så du slipper for løse skiver. Her finder du multigyms fra flere danske webshops.</p><h3>Fordele ved en multigym</h3><p>Bevægelserne er styrede, hvilket gør maskinen sikker og nem at bruge alene – også for begyndere. Du kan typisk træne bryst, ryg, skuldre, arme og ben uden at skifte udstyr.</p><h3>Hvad skal du kigge efter?</h3><p>Se på vægtstakkens størrelse, hvor mange øvelser maskinen giver, og hvor meget plads den kræver. Multigyms fylder og er tunge, så mål både gulv og loftshøjde. Tjek også, om der er plads til at stå ved alle stationer.</p>',
		'faq' => [
			[ 'Er en multigym god til begyndere?', 'Ja, de styrede bevægelser gør den sikker og nem at lære. Du kan træne hele kroppen alene.' ],
			[ 'Hvor meget plads kræver en multigym?', 'En del. Mål både gulvpladsen og loftshøjden, og husk plads omkring maskinen til øvelserne.' ],
			[ 'Kan jeg øge vægten på en multigym?', 'Det afhænger af modellen. Nogle vægtstakke kan udvides. Tjek produktbeskrivelsen.' ],
		],
	],
	'pull-up-stang' => [
		'seo' => '<h2>Pull-up-stang – træn ryg og arme hjemme</h2><p>En pull-up-stang er en af de mest effektive måder at træne ryg, arme og mave med egen kropsvægt. Den fylder næsten intet og kan bruges til pull-ups, chin-ups og hængende benløft. Her finder du pull-up-stænger fra flere danske webshops.</p><h3>Dørkarm, væg eller fritstående?</h3><p>Pull-up-stænger til dørkarmen er nemme at montere og tage ned igen. Stænger til væg eller loft står fast og er mere stabile. Fritstående stationer har ofte også dip-håndtag og kræver ingen montering, men fylder mere.</p><h3>Hvad skal du være opmærksom på?</h3><p>Tjek den maksimale belastning og om stangen passer til din dørkarm eller væg. Er du ny til pull-ups, kan træningselastikker hjælpe dig i gang.</p>',
		'faq' => [
			[ 'Passer en pull-up-stang til alle dørkarme?', 'Nej. Tjek dørkarmens bredde og dybde i forhold til stangens mål.' ],
			[ 'Kan jeg lave pull-ups som begynder?', 'Ja, start med at hænge i stangen eller brug en træningselastik som hjælp, og byg langsomt op.' ],
			[ 'Hvad træner pull-ups?', 'Først og fremmest ryg og arme, men også skuldre og mave.' ],
		],
	],

	// Yoga og pilates.
	'yogamaatte' => [
		'seo' => '<h2>Yogamåtter – greb og komfort</h2><p>En god yogamåtte giver dig greb, så du ikke glider i stillingerne, og polstring, der skåner knæ og ryg. Her finder du yogamåtter fra flere danske webshops.</p><h3>Tykkelse</h3><p>Tynde måtter giver god kontakt til gulvet og stabilitet i stående stillinger og er lette at tage med. Tykkere måtter er mere komfortable til knæ og ryg og passer godt til rolig yoga, pilates og træning på hårdt gulv.</p><h3>Materiale</h3><p>PVC er holdbart og billigt. TPE er lettere og et mere miljøvenligt alternativ. Naturgummi og kork giver godt greb, især når du sveder. Tjek også længden, hvis du er høj.</p>',
		'faq' => [
			[ 'Hvor tyk skal en yogamåtte være?', 'Tynde måtter giver stabilitet, tykke måtter giver komfort. Vælg ud fra, hvilken yoga du laver, og hvor hårdt gulvet er.' ],
			[ 'Hvilket materiale er bedst?', 'Det afhænger af, hvad du prioriterer: PVC er holdbart, TPE er let, og naturgummi og kork giver godt greb.' ],
			[ 'Hvordan rengør jeg min yogamåtte?', 'Tør den af med en fugtig klud og mild sæbe, og lad den tørre helt. Følg producentens anvisning.' ],
		],
	],
	'pilates-reformer' => [
		'seo' => '<h2>Pilates reformer til hjemmet</h2><p>En pilates reformer er en maskine med en kørende slæde og fjedre, der giver modstand i begge retninger. Den bruges til at træne styrke, bevægelighed og kropsholdning med kontrollerede bevægelser. Her finder du pilates reformere fra flere danske webshops.</p><h3>Hvordan fungerer en reformer?</h3><p>Du ligger, sidder eller står på slæden og skubber eller trækker dig mod fjedrene. Ved at skifte fjedre ændrer du modstanden, så maskinen kan bruges af både begyndere og øvede.</p><h3>Hvad skal du kigge efter?</h3><p>Se på længden, den maksimale brugervægt og antallet af fjedre. En reformer fylder meget, men nogle kan klappes sammen eller stilles på højkant. Tjek, om der følger tilbehør med, fx en tower eller en sitting box.</p>',
		'faq' => [
			[ 'Kan begyndere bruge en pilates reformer?', 'Ja, modstanden kan justeres, så den passer til begyndere. Det kan være en god idé at tage et par hold først for at lære øvelserne.' ],
			[ 'Fylder en pilates reformer meget?', 'Ja, den er lang. Nogle modeller kan klappes sammen eller stilles på højkant.' ],
			[ 'Hvad træner man på en reformer?', 'Styrke, bevægelighed og kropsholdning – især core, ben og ryg.' ],
		],
	],
	'yogatilbehor' => [
		'seo' => '<h2>Yogatilbehør – blokke, stropper og bolstere</h2><p>Det rette tilbehør gør det lettere at komme ind i stillingerne og holde dem korrekt. Her finder du yogatilbehør fra flere danske webshops.</p><h3>Yogablokke og stropper</h3><p>En yogablok hjælper dig med at nå gulvet i stillinger, hvor du endnu ikke har bevægeligheden, og giver støtte i siddende stillinger. En yogastrop forlænger armene, så du kan holde fast i fødderne og strække korrekt.</p><h3>Bolstere og puder</h3><p>En bolster er en fast pude, der bruges i restorativ yoga og yin yoga til at støtte kroppen, så du kan slappe helt af. Meditationspuder giver en behagelig siddestilling.</p>',
		'faq' => [
			[ 'Hvad bruges en yogablok til?', 'Den giver støtte og hjælper dig med at nå gulvet, når bevægeligheden endnu ikke rækker.' ],
			[ 'Hvad er en bolster?', 'En fast pude, der støtter kroppen i rolige stillinger, så du kan slappe af.' ],
			[ 'Har jeg brug for tilbehør som begynder?', 'Det er ikke nødvendigt, men blokke og en strop gør mange stillinger lettere og mere sikre.' ],
		],
	],

	// Restitution.
	'massagepistol' => [
		'seo' => '<h2>Massagepistoler til restitution</h2><p>En massagepistol giver hurtige, kraftige stød mod musklen og kan hjælpe med at løsne spændinger og øge blodgennemstrømningen. Her finder du massagepistoler fra flere danske webshops.</p><h3>Hoveder og hastigheder</h3><p>De fleste massagepistoler har flere hastigheder og udskiftelige hoveder: et bredt hoved til store muskler som lår og ryg, og mindre hoveder til afgrænsede punkter. Start på lav hastighed og øg efter behov.</p><h3>Hvad skal du kigge efter?</h3><p>Se på batteritid, vægt og støjniveau – en let og stille pistol er rarere at bruge. Mini-modeller er praktiske i sportstasken, mens større modeller ofte har mere kraft.</p>',
		'faq' => [
			[ 'Hvordan bruger jeg en massagepistol?', 'Før pistolen langsomt over musklen på lav hastighed i kort tid ad gangen. Undgå knogler, led og ømme skader.' ],
			[ 'Hjælper en massagepistol på ømme muskler?', 'Mange oplever, at den løsner spændinger og gør ømhed mere udholdelig. Den kan være et godt supplement til opvarmning og udstrækning.' ],
			[ 'Hvilket hoved skal jeg bruge?', 'Et bredt hoved til store muskelgrupper og et mindre hoved til afgrænsede punkter. Følg producentens anvisning.' ],
		],
	],
	'foam-roller' => [
		'seo' => '<h2>Foam rollers – selvmassage efter træning</h2><p>En foam roller bruges til selvmassage: du ruller kropsvægten frem og tilbage over musklen for at løsne stramme områder og øge bevægeligheden. Her finder du foam rollers fra flere danske webshops.</p><h3>Glat eller riflet?</h3><p>En glat foam roller er blid og god til begyndere. En riflet eller knoppet overflade arbejder dybere og kan føles mere intens. Hårdheden spiller også ind: blødere rollers er mere skånsomme.</p><h3>Størrelse</h3><p>Lange rollers er stabile og gode til ryggen. Korte rollers er nemme at tage med. Der findes også vibrerende foam rollers, som kombinerer rulning med vibration.</p>',
		'faq' => [
			[ 'Hvor ofte skal jeg bruge en foam roller?', 'Du kan bruge den flere gange om ugen, fx efter træning. Start roligt, især hvis musklerne er ømme.' ],
			[ 'Hvilken foam roller skal jeg vælge som begynder?', 'En glat og ikke for hård roller er et godt sted at starte.' ],
			[ 'Må jeg rulle direkte på ryggen?', 'Rul på musklerne, ikke direkte på knogler og led. Vær forsigtig omkring lænden.' ],
		],
	],
	'recovery-boots' => [
		'seo' => '<h2>Recovery boots – kompression til benene</h2><p>Recovery boots er kompressionsstøvler, der skiftevis presser og slipper benene med luft. De bruges af mange løbere og cykelryttere efter hård træning. Her finder du recovery boots fra flere danske webshops.</p><h3>Hvordan fungerer de?</h3><p>Støvlerne har flere luftkamre, der fyldes efter tur – typisk fra foden og op. Du vælger tryk og tid på en lille styreenhed og ligger eller sidder afslappet imens.</p><h3>Hvad skal du kigge efter?</h3><p>Se på antallet af kamre, de mulige trykniveauer og programmer, og om støvlerne passer til din benlængde. Batteridrevne modeller er nemmere at tage med.</p>',
		'faq' => [
			[ 'Hvordan bruger man recovery boots?', 'Tag støvlerne på, vælg tryk og tid, og læn dig tilbage. Start på et lavt tryk.' ],
			[ 'Hvor længe skal man have dem på?', 'Følg producentens anbefaling – mange sessioner varer omkring en halv time.' ],
			[ 'Hvem er recovery boots til?', 'De bruges især af løbere, cykelryttere og andre, der træner meget med benene.' ],
		],
	],
	'kuldeterapi' => [
		'seo' => '<h2>Kuldeterapi og isbad derhjemme</h2><p>Isbade og kuldeterapi er blevet populært både som restitution efter træning og som en del af en fast rutine. Her finder du isbade, kuldekar og tilbehør fra flere danske webshops.</p><h3>Isbad, kar eller tønde?</h3><p>Der findes oppustelige og foldbare kar, der er nemme at stille op og pakke væk, og faste kar og tønder, der kan stå ude hele året. Nogle modeller har en køleenhed, så vandet holder en stabil temperatur.</p><h3>Sikkerhed først</h3><p>Start med korte ophold og ikke for koldt vand, og bad aldrig alene, hvis du er ny. Har du hjerte-kar-sygdom eller andre helbredsproblemer, så tal med din læge først.</p>',
		'faq' => [
			[ 'Hvor længe skal man være i et isbad?', 'Start med korte ophold og byg langsomt op. Lyt til kroppen og følg anbefalingerne for det udstyr, du bruger.' ],
			[ 'Virker isbad efter træning?', 'Mange oplever, at det mindsker ømhed. Forskningen er delt om effekten på selve restitutionen.' ],
			[ 'Kan et isbad stå udenfor?', 'Mange kar og tønder er beregnet til udendørs brug. Tjek produktbeskrivelsen.' ],
		],
	],

	// Tilbehør.
	'traeningsmaatte' => [
		'seo' => '<h2>Træningsmåtter – til gulvøvelser og under maskiner</h2><p>En træningsmåtte beskytter både gulv og krop. Den bruges til gulvøvelser som planke og mavebøjninger og under maskiner og vægte. Her finder du træningsmåtter fra flere danske webshops.</p><h3>Måtte til øvelser eller til gulvet?</h3><p>Til gulvøvelser er en blød, behagelig måtte rar for knæ og ryg. Under løbebånd, cykler og vægte er en fast gummimåtte eller gummifliser bedre: de beskytter gulvet, dæmper lyd og ligger stille.</p><h3>Hvad skal du kigge efter?</h3><p>Se på tykkelse, størrelse og overflade. En skridsikker overflade gør måtten mere sikker. Puslespilsmåtter kan sættes sammen til et helt hjemmegym.</p>',
		'faq' => [
			[ 'Hvilken måtte skal ligge under et løbebånd?', 'En fast gummimåtte beskytter gulvet og dæmper vibrationer. Vælg en, der er større end løbebåndet.' ],
			[ 'Hvad er forskellen på en træningsmåtte og en yogamåtte?', 'Træningsmåtter er ofte tykkere og mere robuste. Yogamåtter er tyndere og giver bedre greb og stabilitet.' ],
			[ 'Kan måtter sættes sammen?', 'Ja, puslespilsmåtter og gummifliser kan sættes sammen til et større område.' ],
		],
	],
	'elastikker' => [
		'seo' => '<h2>Træningselastikker til styrke og opvarmning</h2><p>Træningselastikker er lette, billige og kan bruges til alt fra styrketræning og opvarmning til genoptræning og hjælp til pull-ups. Her finder du elastikker fra flere danske webshops.</p><h3>Typer af elastikker</h3><p>Loop bands er korte elastikker, der bruges om benene til baller og hofter. Lange powerbands bruges til styrkeøvelser og som hjælp til pull-ups. Rør-elastikker med håndtag minder om kabeltræk.</p><h3>Modstand</h3><p>Elastikker fås i flere modstande, ofte farvekodet fra let til hård. Et sæt med flere modstande gør det let at variere og øge belastningen.</p>',
		'faq' => [
			[ 'Hvilken modstand skal jeg vælge?', 'Et sæt med flere modstande er et godt valg, så du kan starte let og øge, efterhånden som du bliver stærkere.' ],
			[ 'Kan elastikker hjælpe til pull-ups?', 'Ja, en lang elastik om stangen og under foden eller knæet tager noget af vægten.' ],
			[ 'Holder elastikker længe?', 'Opbevar dem tørt og væk fra sol og skarpe kanter. Tjek dem jævnligt for revner.' ],
		],
	],
	'boksning' => [
		'seo' => '<h2>Boksudstyr til træning hjemme</h2><p>Boksning giver hård konditionstræning og træner samtidig teknik og koordination. Her finder du boksehandsker, boksesække, håndbind og andet boksudstyr fra flere danske webshops.</p><h3>Boksehandsker</h3><p>Boksehandsker angives i ounces (oz). Tungere handsker giver mere polstring og beskytter både dig og din makker. Til sæktræning bruger mange lettere handsker, mens sparring kræver tungere. Brug altid håndbind under handskerne.</p><h3>Boksesæk</h3><p>En hængende boksesæk kræver solid montering i loft eller stativ. Fritstående sække fyldes med vand eller sand i foden. Jo tungere sækken er, jo mindre bevæger den sig.</p>',
		'faq' => [
			[ 'Hvilken størrelse boksehandsker skal jeg vælge?', 'Det afhænger af din vægt og træningen. Tungere handsker giver mere beskyttelse, især til sparring.' ],
			[ 'Skal jeg bruge håndbind?', 'Ja, håndbind støtter håndled og knoer og bør bruges under handskerne.' ],
			[ 'Kan jeg have en boksesæk i lejlighed?', 'En fritstående sæk er lettest, men slag og spark kan give lyd og vibrationer. Tænk på naboerne.' ],
		],
	],
	'traeningsbolde' => [
		'seo' => '<h2>Træningsbolde – fra gymball til slam ball</h2><p>Træningsbolde bruges til alt fra core-træning og balance til eksplosive kast. Her finder du træningsbolde fra flere danske webshops.</p><h3>Typer af bolde</h3><p>En gymball eller balancebold bruges til core- og stabilitetsøvelser og som alternativ til en kontorstol. Medicinbolde bruges til kast og styrkeøvelser. Slam balls er bygget til at blive kastet hårdt i gulvet uden at hoppe tilbage. Wall balls er bløde og bruges til kast mod væg.</p><h3>Størrelse og vægt</h3><p>En gymball vælges efter din højde, så hofterne er i højde med knæene, når du sidder. Medicin- og slam balls vælges efter vægt – start let, så du kan holde god teknik.</p>',
		'faq' => [
			[ 'Hvilken størrelse gymball skal jeg vælge?', 'Vælg efter højde, så du sidder med hofterne i knæhøjde. Producenten angiver typisk hvilke højder bolden passer til.' ],
			[ 'Hvad er forskellen på en medicinbold og en slam ball?', 'En slam ball er bygget til at blive kastet i gulvet og hopper ikke tilbage. En medicinbold bruges til kast og øvelser.' ],
			[ 'Hvilken vægt skal min slam ball have?', 'Start med en vægt, du kan kaste mange gange med god teknik, og øg derefter.' ],
		],
	],
	'sjippetov' => [
		'seo' => '<h2>Sjippetov – billig og effektiv kondition</h2><p>Et sjippetov er noget af det billigste og mest effektive udstyr til konditionstræning, og det kan bruges næsten overalt. Her finder du sjippetove fra flere danske webshops.</p><h3>Hvilket sjippetov?</h3><p>Et almindeligt tov med stof- eller PVC-line er godt til begyndere. Et speed rope med tynd line og kuglelejer i håndtagene drejer hurtigere og bruges til dobbeltunders. Vægtede tove træner også arme og skuldre.</p><h3>Den rigtige længde</h3><p>Stil dig på midten af tovet – håndtagene bør nå omtrent til armhulerne. Mange tove kan justeres i længden. Hop på et underlag, der giver lidt efter, fx en træningsmåtte.</p>',
		'faq' => [
			[ 'Hvordan finder jeg den rigtige længde?', 'Stil dig på midten af tovet. Håndtagene bør nå omtrent til armhulerne.' ],
			[ 'Hvad er et speed rope?', 'Et sjippetov med tynd line og kuglelejer, der drejer hurtigt. Det bruges til hurtige hop og dobbeltunders.' ],
			[ 'Er sjippetov god træning?', 'Ja, det giver høj puls og træner kondition, koordination og læg på kort tid.' ],
		],
	],
];
