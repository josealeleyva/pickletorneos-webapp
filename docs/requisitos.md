**PROYECTO “Punto de Oro”/ GESTIÓN DE TORNEOS**

**Para el organizador:**

### **Épica 1: Autenticación y Perfiles de Organizador**

**Descripción:** Permitir a los organizadores registrarse, iniciar sesión y gestionar su perfil y la información de sus complejos.

* **Historia de Usuario: Registro de Organizador**  
  * Como organizador de torneos, quiero poder registrarme en el sistema para obtener acceso al panel de gestión.  
* **Historia de Usuario: Inicio de Sesión y Cierre de Sesión**  
  * Como organizador, quiero poder iniciar y cerrar sesión de forma segura para acceder y proteger mi panel de gestión.  
* **Historia de Usuario: Gestión de Perfil de Organizador**  
  * Como organizador, quiero poder ver y editar la información de mi perfil (nombre, email, contacto) para mantener mis datos actualizados.  
* **Historia de Usuario: Gestión de Complejos Deportivos**  
  * Como organizador, quiero poder agregar y gestionar la información de uno o varios complejos deportivos (nombre, dirección, canchas) para organizar torneos en diferentes ubicaciones dentro de una localidad.  
  * **Criterios de Aceptación:**  
    * El organizador puede agregar un nuevo complejo.  
    * Puede editar los detalles de un complejo existente.  
    * Puede definir las canchas disponibles dentro de un complejo (ej. PadelCenter \- Cancha 1, Cancha 2).  
    * Puede eliminar un complejo (con advertencia si tiene torneos asociados).

---

### **Épica 2: Gestión de Torneos**

**Descripción:** Proporcionar al organizador las herramientas completas para crear, configurar y administrar torneos de pádel (y a futuro, otros deportes).

* **Historia de Usuario: Creación de Nuevo Torneo**  
  * Como organizador, quiero poder crear un nuevo torneo para iniciar la planificación de un evento.  
  * **Criterios de Aceptación:**  
    * El organizador puede especificar: Nombre del Torneo, Descripción, Deporte (por defecto Padel), Fechas de Inicio/Fin, Complejo.  
    * Puede definir un costo de inscripción (opcional).  
* **Historia de Usuario: Configuración de Formato de Torneo**  
  * Como organizador, quiero poder seleccionar y configurar el formato del torneo (Eliminación Directa, Fase de Grupos \+ Eliminación) para adaptar el torneo a mis necesidades.  
  * **Criterios de Aceptación:**  
    * El organizador puede elegir entre "Eliminación Directa" o "Fase de Grupos y Eliminación Directa".  
    * Si es Fase de Grupos, puede definir el número de grupos y/o el tamaño de los grupos.  
    * Si es Fase de Grupos, puede configurar la lógica de cruces para la fase de eliminación (ej. 1ro Grupo A vs 2do Grupo B).  
* **Historia de Usuario: Gestión de Participantes y Equipos/Parejas**  
  * Como organizador, quiero poder agregar jugadores manualmente y gestionar equipos/parejas para inscribir a los participantes en mi torneo.  
  * **Criterios de Aceptación:**  
    * Puede buscar y agregar jugadores ya registrados en el sistema.  
    * Puede crear un nuevo perfil de jugador al agregarlo a un torneo (si no existe).  
    * Puede formar parejas (para pádel) o equipos (para fútbol) con los jugadores inscritos.  
    * Puede asignar categorías a los jugadores/parejas (ej. 8va, 7ma, 6ta para pádel / \+30, libre, \+40 para fútbol).  
* **Historia de Usuario: Gestión de Partidos y Horarios**  
  * Como organizador, quiero poder programar los partidos, asignar canchas y definir horarios para organizar la logística del torneo.  
  * **Criterios de Aceptación:**  
    * El sistema genera automáticamente los partidos según el formato y los participantes aunque también debe tener la posibilidad de hacer todo de manera manual.  
    * El organizador puede asignar una cancha y una fecha/hora a cada partido.  
    * Existe una vista de calendario o agenda para ver todos los partidos programados.  
    * Puede modificar la cancha o el horario de un partido programado.  
* **Historia de Usuario: Carga de Resultados de Partidos**  
  * Como organizador, quiero poder cargar los resultados de los partidos para que el sistema avance automáticamente en el torneo.  
  * **Criterios de Aceptación:**  
    * El organizador puede ingresar el resultado de cada partido (ej. sets ganados).  
    * El sistema valida la entrada de resultados.  
    * Una vez cargado el resultado, el sistema identifica automáticamente al ganador y lo avanza en la llave/fase de grupos. Se deben definir los criterios de avance, es decir, primero partidos ganados, luego sets, luego juegos, luego enfrentamientos directos, etc.  
* **Historia de Usuario: Visualización del Cuadro del Torneo y Posiciones**  
  * Como organizador, quiero poder ver el cuadro del torneo (llaves) y las tablas de posiciones de los grupos actualizadas para tener una visión general del progreso.  
  * **Criterios de Aceptación:**  
    * Se muestra una representación gráfica de las llaves de eliminación.  
    * Se actualizan automáticamente las posiciones en las fases de grupo.  
    * Es fácil identificar los próximos cruces.

---

### **Épica 3: Comunicación y Notificaciones para Organizadores**

**Descripción:** Proporcionar al organizador herramientas para comunicarse con los jugadores y gestionar notificaciones.

* **Historia de Usuario: Envío de Mensajes Masivos a Jugadores**  
  * Como organizador, quiero poder enviar un mensaje a todos los participantes de un torneo o a un grupo específico para comunicar información relevante (ej. cambios, avisos).  
  * **Criterios de Aceptación:**  
    * El organizador puede redactar un mensaje.  
    * Puede seleccionar el público (todos los participantes del torneo, jugadores de una categoría, etc.).  
    * El mensaje se envía como notificación push a la app de los jugadores.  
    * (Opcional, si se implementa como pago): Opción de enviar también vía WhatsApp (con consentimiento del jugador y cobro asociado).  
    * Opción vía mail.  
* **Historia de Usuario: Configuración de Notificaciones Automáticas**  
  * Como organizador, quiero poder configurar qué notificaciones automáticas se envían a los jugadores (ej. recordatorios de partidos, cambios de horario) para mantenerlos informados sin intervención manual.  
  * **Criterios de Aceptación:**  
    * El organizador puede activar/desactivar tipos de notificaciones automáticas.  
    * Puede definir el tiempo de anticipación para los recordatorios de partidos (ej. 30 minutos antes).  
* **Historia de Usuario: Registro de Actividad de Torneo**  
  * Como organizador, quiero poder ver un registro de los eventos importantes del torneo (cambios de horario, resultados cargados, mensajes enviados) para tener un historial de la gestión.  
  * **Criterios de Aceptación:**  
    * Se muestra un feed de actividad en el panel del torneo.  
    * Registra acciones clave realizadas por el organizador o el sistema.

---

### **Épica 4: Ranking y Categorías (Gestión de Jugadores)**

**Descripción:** Mantener un sistema de ranking y categorías de jugadores para la correcta organización de torneos por nivel.

* **Historia de Usuario: Gestión de Categorías de Jugadores**  
  * Como organizador, quiero poder definir y gestionar las categorías de jugadores (ej. 8va, 7ma, 6ta) por deporte para clasificar a los jugadores y organizar torneos adecuados.  
  * **Criterios de Aceptación:**  
    * Puede crear, editar y eliminar categorías.  
    * Las categorías son específicas por deporte (ej. Pádel: 8va, 7ma, 6ta / Fútbol: libre, \+30).  
    * Puede asignar jugadores a categorías.  
* **Historia de Usuario: Visualización del Ranking de Jugadores**  
  * **Como** organizador, **quiero** poder ver el ranking de los jugadores que han participado en mis torneos **para** identificar sus niveles de juego.  
  * **Criterios de Aceptación:**  
    * Se muestra un listado de jugadores con su categoría actual.  
    * (Futuro): Si se implementa un sistema de puntos, se muestra su puntuación y posición.  
    * Puede filtrar el ranking por deporte o categoría.

## **Para el jugador**

### **Épica 1: Autenticación y Perfil de Jugador**

**Descripción:** Permitir a los jugadores registrarse, iniciar sesión, gestionar su perfil y seleccionar sus preferencias.

* **Historia de Usuario: Registro de Jugador**  
  * Como nuevo jugador, quiero poder registrarme en la app para comenzar a utilizar el sistema y unirme a torneos.  
  * **Criterios de Aceptación:**  
    * El jugador puede registrarse con email y contraseña, o a través de redes sociales (Google/Apple ID).  
    * Se solicita y se guarda la selección de su **deporte principal (ej. Pádel)**.  
    * Se solicita y se guarda su nombre y apellido.  
    * Se envía un email de verificación (si usa email/contraseña) y la cuenta se activa.  
* **Historia de Usuario: Inicio y Cierre de Sesión del Jugador**  
  * Como jugador, quiero poder iniciar y cerrar sesión de forma segura para acceder y proteger mi información de torneos y perfil.  
  * **Criterios de Aceptación:**  
    * El jugador puede iniciar sesión con sus credenciales.  
    * Manejo correcto de credenciales inválidas.  
    * Opción para "Recordar usuario" (mantener sesión).  
    * El jugador puede cerrar sesión desde cualquier parte de la app.  
* **Historia de Usuario: Gestión del Perfil del Jugador**  
  * Como jugador, quiero poder ver y editar la información de mi perfil (nombre, foto, email, teléfono, deporte, complejo habitual, categorías) para mantener mis datos actualizados y relevantes.  
  * **Criterios de Aceptación:**  
    * El jugador puede acceder a su perfil desde el menú principal.  
    * Puede cargar/cambiar su foto de perfil.  
    * Puede actualizar su nombre, email, teléfono.  
    * Puede cambiar su deporte principal.  
    * Se muestra su categoría de jugador (si ha sido asignada por un organizador).  
    * Los cambios se guardan y reflejan correctamente.

---

### **Épica 2: Visualización del Torneo en Vivo**

**Descripción:** Proporcionar a los jugadores acceso en tiempo real a la información de los torneos en los que están inscritos.

* **Historia de Usuario: Mis Torneos**  
  * Como jugador, quiero ver un listado de todos los torneos en los que estoy inscripto o he participado para tener un acceso rápido a mis eventos.  
  * **Criterios de Aceptación:**  
    * Hay una sección "Mis Torneos" en el menú principal.  
    * Muestra torneos activos y torneos pasados.  
    * Cada elemento del listado me lleva a la pantalla de detalles del torneo.  
* **Historia de Usuario: Ver Horarios y Canchas de Mis Partidos**  
  * Como jugador, quiero poder ver los horarios y la cancha asignada a mis próximos partidos para prepararme y llegar a tiempo.  
  * **Criterios de Aceptación:**  
    * En la pantalla de detalles de mi torneo, hay una sección clara con mis próximos partidos.  
    * Cada partido muestra: la fecha, la hora, la cancha asignada y los oponentes.  
    * Los partidos pasados muestran el resultado.  
* **Historia de Usuario: Visualizar Cruces y Llaves del Torneo**  
  * Como jugador, quiero poder ver el cuadro completo del torneo (llaves de eliminación) actualizado para seguir el progreso del evento y ver mis posibles próximos oponentes.  
  * **Criterios de Aceptación:**  
    * Se muestra una representación gráfica e interactiva de las llaves del torneo.  
    * Se resaltan mis propios partidos/caminos.  
    * Los resultados de los partidos ya jugados se reflejan en el cuadro.  
    * En fase de grupos, se muestran las tablas de posiciones actualizadas.  
* **Historia de Usuario: Consultar Resultados y Posiciones**  
  * Como jugador, quiero poder ver los resultados de todos los partidos del torneo y las tablas de posiciones de los grupos para mantenerme informado sobre el progreso general del evento.  
  * **Criterios de Aceptación:**  
    * Hay una sección de "Resultados" dentro del torneo.  
    * Se listan los resultados de los partidos completados.  
    * Para torneos con fase de grupos, se muestran las tablas de posiciones actualizadas.  
* **Historia de Usuario: Consultar Ranking de Jugadores**  
  * Como jugador, quiero poder ver el ranking de jugadores (general y por categoría/deporte) para conocer mi posición y la de otros.  
  * **Criterios de Aceptación:**  
    * Existe una sección de "Ranking" accesible desde el menú principal o dentro de un torneo.  
    * Muestra un listado de jugadores ordenados por su ranking.  
    * Se puede filtrar el ranking por deporte y categoría.

---

### **Épica 3: Notificaciones y Comunicación (App)**

**Descripción:** Asegurar que los jugadores reciban información relevante y oportuna sobre sus torneos.

* **Historia de Usuario: Recibir Notificaciones Push**  
  * Como jugador, quiero recibir notificaciones push en mi teléfono para ser alertado sobre información importante de mis torneos (ej. recordatorios, cambios de horario).  
  * **Criterios de Aceptación:**  
    * Recibo notificaciones push automáticas sobre mis próximos partidos (ej. 30 min antes).  
    * Recibo notificaciones push sobre cambios de horario o cancha de mis partidos.  
    * Recibo notificaciones push de mensajes masivos enviados por el organizador.  
    * (Opcional, si se implementa el add-on de WA): Recibo notificaciones vía WhatsApp.  
* **Historia de Usuario: Historial de Notificaciones**  
  * Como jugador, quiero poder ver un historial de las notificaciones que he recibido para revisar información importante que pude haber omitido.  
  * **Criterios de Aceptación:**  
    * Existe una sección de "Notificaciones" en la app.  
    * Muestra un listado cronológico de todas las notificaciones recibidas.  
    * Puedo marcar notificaciones como leídas/no leídas.

**WEB (Premium)**

Al pagar un plan premium se les da un subdominio de nuestra web en la que podrán colgar noticias, banners y fotos de los torneos.

**PROCESO**

marcar requisitos de cada step: Registro \- Creación \- Pago \- Visualización \- Envío de notificaciones. 

BENCHMARKING: [https://www.padelorganizer.com.ar/funciones.php](https://www.padelorganizer.com.ar/funciones.php) en esta página se ven las funcionalidades que puede tener la app

REGLAMENTOS Y LLAVES: [https://padel.org.ar/wp-content/uploads/2025/02/REGLAMENTO-DEPORTIVO-AMATEUR-APA.pdf](https://padel.org.ar/wp-content/uploads/2025/02/REGLAMENTO-DEPORTIVO-AMATEUR-APA.pdf) acá se ve como funciona el esquema de llaves y demás particularidades que tienen los torneos de pádel

**APP TORNEOS**

**Pasos y Requisitos:**

1. Al ingresar por primera vez a la app debo registrarme, ya sea como organizador o como jugador. (Ver diseño de logo)  
2. Lo primero es señalar como que me quiero registrar, en caso de jugador debo cargar nombre y apellido, mail y teléfono, opcional foto. En caso de organizador lo mismo y en ambos casos con contraseña y confirmación de contraseña. Como organizador puedo agregar además el nombre de mi organización. (ver métodos de registro vía mail, ver recuperación de contraseña), además debo poder ver los términos y condiciones y aceptarlos en un checkbox.  
3. El siguiente paso ya sería una vista previa al panel de administración de torneos o ver los torneos vigentes en caso de ser jugador.  
4. Además de esta pantalla, se debe poder ver mi perfil en ambos casos y en lo posible en caso de ser la primera vez que ingreso un tutorial in-app para conocer las funcionalidades de la App  
5. Ahora bien, en el tablero de gestión del torneo hay varias configuraciones que realizar. Lo primero es cargar la información general del torneo, darle un nombre al torneo, una descripción opcional, fecha de inicio y fecha de finalización, fecha límite de inscripción opcional, imagen o banner opcional, las categorías que corresponden en el torneo (8va, 7ma, 6ta, etc.), el formato (liga, eliminación directa, grupos más eliminatoria, round robin, etc.), cargar los jugadores/parejas, premios (opcional).  
   1. El siguiente paso es la gestión de jugadores/parejas (la carga de estos puede hacerse antes desde otra sección), para esto se debe poder cargar los nombres y apellidos obligatorio y de manera opcional foto, mail y teléfono (debemos ver el método para vincular jugadores que luego usen la app por la cuestión de las notificaciones).  
   2. Paso siguiente es la carga de los mismos en los grupos/llaves, se deben generar algoritmos que los distribuyan de manera automáticamente con la posibilidad de ajustarlo manualmente y con otra posibilidad que sea de asignar cabezas de serie para una distribución equitativa. Los grupos pueden ser de la cantidad de parejas que se considere pertinente. Ahora bien también debe elegirse los criterios de avance, cuantos avanzan por grupo (dos o más, dos y los mejores terceros, etc) casos de desempate (diferencia de sets, games, partidos ganados, enfrentamientos directos).  
   3. Lo siguiente es el complejo y las canchas, con la creación del mismo o selección en caso de que ya se haya creado con los datos de nombre, dirección y datos de contacto del complejo. Las canchas también cumplen el mismo criterio con la posibilidad de formar parte de un complejo.  
   4. El último paso es la asignación de horarios y canchas. a cada cruce debe poder asignar un horario estimativo con una posible aclaración por ejemplo al término del partido entre rival 1 y rival 2, y una cancha. debe existir la posibilidad de mover la sede por cualquier cuestión ajena a la organización.  
6. El último paso para la creación del torneo es el pago por el torneo creado. Al finalizar te debe redireccionar al pago mediante mercado pago. Realizado el pago el torneo queda creado y siguen los pasos siguientes que son la gestión de los avances y las notificaciones a los jugadores.  
7. El envío de notificaciones es el último paso en la gestión de un torneo previo a la finalización del mismo a través de la gestión del avance del mismo. Para esto debo poder seleccionar los destinatarios, el mensaje con la información que voy a mandar la cual puede ser los horarios con las canchas al comienzo del torneo y a medida que se avance añadiendo los rivales que tocan, un recordatorio media hora antes de empezar y debe tener un marcador de visto para un mejor control. Tener en cuenta si esto se realiza dentro de la app o a través de wpp.  
8. Se debe guardar el historial de los torneos organizados en una sección aparte.

**\*Lógica de clasificación y sembrado de parejas en llaves eliminatorias:\*** 

## **Casos Posibles de Clasificación y Avance**

Considerando un máximo de 64 parejas y las reglas establecidas, la variabilidad principal estará en cómo se estructuran los grupos y cuántas parejas avanzan de cada uno para llegar a una fase de playoffs con una potencia de 2 (8, 16, 32, 64).

### **Variables Clave:**

1. **Número Total de Parejas:** Desde 4 hasta 64\.  
2. **Tamaño de los Grupos:** 3, 4 o 5 parejas por grupo.  
3. **Regla de Clasificación por Grupo:**  
   * **Primeros:** Solo la pareja en 1ª posición.  
   * **Primeros y Segundos:** Las dos primeras parejas.  
   * **Primeros y Terceros:** Las tres primeras parejas (solo posible en grupos de 5).  
   * **Primeros \+ Mejores Segundos:** 1º de cada grupo \+ "X" cantidad de mejores 2ºs de todos los grupos.  
   * **Primeros, Segundos \+ Mejores Terceros:** 1º y 2º de cada grupo \+ "X" cantidad de mejores 3ºs de todos los grupos.

### **Criterios de Desempate y BYES:**

Los **BYES** (parejas que no juegan la primera ronda y pasan directamente) son fundamentales cuando el número de clasificados no es una potencia de 2, o para beneficiar a los mejores clasificados. Los criterios para determinar quiénes reciben los byes, así como para desempatar a los "mejores segundos/terceros" son:

1. **Partidos Ganados**  
2. **Diferencia de Sets** (Sets a favor \- Sets en contra)  
3. **Diferencia de Games** (Games a favor \- Games en contra)  
4. **Enfrentamiento Directo** (si aplica entre dos parejas con todo lo demás igual)

Esto significa que tu sistema debe ser capaz de calcular y ordenar a las parejas según estos criterios para definir tanto a los "mejores clasificados" como a los beneficiarios de los byes.

---

## **Esquema Visual de Casos Posibles**

En lugar de listar cada combinación numérica (lo cual sería abrumador), vamos a crear un diagrama de flujo que muestre el **proceso de decisión y las ramificaciones** según las configuraciones elegidas. Esto dará una comprensión clara de la lógica detrás de las diferentes posibilidades.

FRAGMENTO DE CÓDIGO

graph TD

    A\[Inicio del Torneo\] \--\> B{Definir Cantidad Total de Parejas (4-64)};

    B \--\> C{Configurar Grupos};

    subgraph Configuración de Grupos

        C \--\> C1\[Número de Grupos\];

        C1 \--\> C2{Tamaño del Grupo?};

        C2 \-- 3 Parejas \--\> G3\[Grupos de 3\];

        C2 \-- 4 Parejas \--\> G4\[Grupos de 4\];

        C2 \-- 5 Parejas \--\> G5\[Grupos de 5\];

        G3 & G4 & G5 \--\> D{Definir Regla de Avance a Playoffs};

    end

    subgraph Reglas de Avance por Grupo

        D \--\> D1{Cuántas Parejas Clasifican de cada Grupo?};

        D1 \-- Sólo 1° \--\> P1\[Pasan solo los Primeros\];

        D1 \-- 1° y 2° \--\> P2\[Pasan Primeros y Segundos\];

        D1 \-- 1°, 2° y 3° (solo si grupo es de 5\) \--\> P3\[Pasan Primeros, Segundos y Terceros\];

        D1 \-- X Mejores 2°s \--\> PM2\[Pasan 1°s \+ X Mejores 2°s de todos los grupos\];

        D1 \-- X Mejores 3°s \--\> PM3\[Pasan 1°s, 2°s \+ X Mejores 3°s de todos los grupos\];

        P1 & P2 & P3 & PM2 & PM3 \--\> E{Calcular Total de Clasificados};

    end

    subgraph Ajuste para Playoffs

        E \--\> F{Total de Clasificados es Potencia de 2?};

        F \-- Sí (8, 16, 32, 64\) \--\> G\[Armar Cuadro de Eliminación Directa Completo\];

        F \-- No \--\> H{Aplicar BYES para los Mejores Clasificados};

        H \--\> G;

    end

    subgraph Definición de BYES y Criterios

        H \--\> H1\[Cálculo de Clasificación para BYES y Mejores X:\];

        H1 \--\> H2\[1. Partidos Ganados\];

        H2 \--\> H3\[2. Diferencia de Sets\];

        H3 \--\> H4\[3. Diferencia de Games\];

        H4 \--\> H5\[4. Enfrentamiento Directo\];

        H5 \--\> I\[Asignar BYES a los Mejor Clasificados\];

    end

    G \--\> J\[Emparejamientos \- Ronda 1\];

    J \--\> K\[Partidos de Eliminatoria\];

    K \--\> L\[Ganadores Avanzan\];

    L \--\> M\[Gran Final\];

    M \--\> N\[Campeones\!\];

 **Explicación del Esquema Visual:**

1. **Inicio y Configuración Base:**

   * Comenzamos con la **cantidad total de parejas** (desde 4 hasta 64). Esto definirá la escala del torneo.  
   * Luego, se decide la **configuración de los grupos**: ¿serán de 3, 4 o 5 parejas? Esto impacta directamente en cuántos grupos tendremos y el número de partidos en la fase inicial.  
2. **Reglas de Avance por Grupo:**

   * Aquí es donde se define la "dificultad" para clasificar. Las opciones son claras:  
     * **Solo los 1°s:** La más restrictiva.  
     * **1°s y 2°s:** Un avance más común.  
     * **1°s, 2°s y 3°s:** Solo en grupos de 5 parejas, para torneos con muchos clasificados.  
     * **Con "Mejores X":** Estas opciones son clave cuando se necesita ajustar el número de clasificados a una potencia de 2\. Aquí entran los **criterios de desempate** para determinar quiénes son esos "mejores".  
3. **Ajuste para Playoffs (El Momento Clave):**

   * Una vez que se calculan todos los clasificados según las reglas de grupo, el sistema debe verificar: **¿El número total de clasificados es una potencia de 2 (8, 16, 32, 64)?**  
   * **Si es "Sí"**: ¡Perfecto\! Se arma el cuadro de eliminación directa de manera lineal.  
   * **Si es "No"**: Aquí es donde entran los **BYES**. El sistema identifica cuántas parejas necesitan un bye para que el cuadro quede con una potencia de 2\.  
4. **Definición de BYES y Criterios de Desempate:**

   * Los **BYES se asignan a las parejas mejor clasificadas** según un ranking de rendimiento general del grupo.  
   * Este ranking se determina por la secuencia de criterios que mencionaste: **Partidos Ganados \> Diferencia de Sets \> Diferencia de Games \> Enfrentamiento Directo**. Tu sistema necesita ser capaz de aplicar esta lógica para ordenar a todas las parejas clasificadas.  
5. **Fase de Eliminación Directa (Playoffs):**

   * Finalmente, con el número de clasificados ajustado (y byes asignados), se arma el cuadro de eliminación. Los emparejamientos seguirán la lógica de "sembrado" (primeros de grupo contra segundos, buscando evitar cruces tempranos entre los mejores).  
   * El proceso es clásico: partidos, ganadores avanzan hasta la gran final.

EXPLICACIÓN DE MEJORES SEGUNDOS

## **¿Necesitas "Mejores Segundos"? La Flexibilidad es Clave**

La respuesta corta es: **sí, probablemente los necesitarás en algunos casos para lograr un cuadro de playoffs con una potencia de 2 (8, 16, 32, 64 parejas).**

Vamos a desglosar por qué y cómo gestionarlo:

### **¿Cuándo son Necesarios los "Mejores Segundos" o "Mejores Terceros"?**

La función principal de los "mejores X" (ya sean segundos o terceros) es **ajustar el número de clasificados** para que coincida con la estructura de un cuadro de eliminación directa.

Imagina los siguientes escenarios:

* **Escenario A: Grupos pequeños, pocos clasificados por grupo, pero necesitas muchos para playoffs.**

  * **Ejemplo:** Tienes 30 parejas y las divides en 10 grupos de 3\. Decides que solo pasa el primer clasificado de cada grupo. ¡Tienes 10 clasificados\! Pero para un cuadro de eliminación, necesitas 8 o 16\. Aquí los "mejores segundos" son cruciales.  
    * Si quieres 8 clasificados, es un problema porque tienes 10\. Tendrías que eliminar a 2 primeros clasificados, lo cual es muy injusto.  
    * Si quieres 16 clasificados, te faltan 6\. Ahí es donde entran los **6 mejores segundos** de esos 10 grupos.  
* **Escenario B: El número de grupos y clasificados no encaja justo.**

  * **Ejemplo:** Tienes 40 parejas y las divides en 8 grupos de 5\.  
    * Si solo pasan los primeros de cada grupo: tienes 8 clasificados (1° de cada grupo). ¡Perfecto para un cuadro de 8\! No necesitas "mejores segundos".  
    * Si pasan los primeros y segundos de cada grupo: tienes 16 clasificados (2 por grupo x 8 grupos). ¡Perfecto para un cuadro de 16\! No necesitas "mejores segundos".  
    * **Pero, ¿qué pasa si solo clasificas a 1.5 parejas por grupo?** Esto es lo que resuelve "mejores segundos".  
      * Si necesitas un cuadro de 16 y solo tienes 8 grupos de 4 parejas, con la regla de que solo pasa el primero. Tienes 8 clasificados (los primeros de cada grupo). Para llegar a 16, necesitarías **8 mejores segundos**.

**En resumen:** Los "mejores X" son tu **válvula de ajuste**. Te permiten clasificar a un número fijo de equipos por grupo (por ejemplo, "el primero de cada grupo") y luego "rellenar" los cupos restantes en tu llave de playoffs con los equipos con mejor rendimiento que quedaron justo por debajo del corte principal.

### **¿Cómo lo explico a mi equipo sin que se confundan?**

En lugar de enfocarse en todas las combinaciones posibles de "mejores segundos" (lo cual es complejo de visualizar), enfóquense en el **propósito** y el **mecanismo**.

**Propuesta de explicación:**

"Chicos, necesitamos que el número total de parejas que avanzan a los playoffs siempre sea una **potencia de 2** (8, 16, 32 o, en el extremo, 64). Esto nos simplifica enormemente el cuadro de eliminación directa.

Cuando configuremos los grupos y las reglas de clasificación, como 'pasa el primero de cada grupo' o 'pasan el primero y el segundo', a veces el número total de clasificados **no va a ser una potencia de 2**.

Ahí es donde entra el concepto de **'Mejores Segundos' (o 'Mejores Terceros' si aplica)**. Si necesitamos llegar a 16 parejas, y con los primeros de cada grupo solo tenemos 10, buscaremos a los **6 segundos clasificados con mejor rendimiento** de todos los grupos y los sumaremos a los primeros.

**¿Cómo definimos 'mejor rendimiento'?** Usaremos el ranking que ya definimos:

1. **Más Partidos Ganados.**  
2. **Mejor Diferencia de Sets.**  
3. **Mejor Diferencia de Games.**  
4. **Enfrentamiento Directo** (si aún hay empate entre dos).

Así, siempre podremos armar una llave de playoffs justa y organizada, y los BYEs se usarán para dar una ventaja a los mejores equipos cuando no haya suficientes partidos en la primera ronda."
