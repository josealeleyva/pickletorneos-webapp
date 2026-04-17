# Modelo de Negocio - PickleTorneos

## 1. Resumen Ejecutivo

**PickleTorneos** es una plataforma integral de gestión de torneos deportivos (inicialmente enfocada en pádel) que conecta a organizadores con jugadores, facilitando la administración completa de eventos deportivos desde la creación hasta la finalización del torneo.

### Propuesta de Valor

**Para Organizadores:**
- Sistema completo de gestión de torneos (inscripciones, llaves, resultados, horarios)
- Automatización de notificaciones a jugadores
- Gestión de complejos y canchas
- Ahorro de tiempo en tareas administrativas
- Profesionalización de sus torneos

**Para Jugadores:**
- Acceso en tiempo real a información de sus torneos
- Notificaciones automáticas de partidos y cambios
- Visualización de llaves, resultados y rankings
- Historial de participación en torneos

---

## 2. Modelo de Ingresos

### 2.1. Estructura de Precios Principal

**Cobro por Torneo Creado:**
- **Primer torneo:** GRATIS (estrategia de adquisición)
- **Torneos subsiguientes:** $25.000 ARS por torneo

**Justificación del precio:**
- Ahorro de tiempo significativo para el organizador (estimado 5-10 horas por torneo)
- Profesionalización del evento
- Acceso a funcionalidades completas sin límites
- Sin costos de suscripción mensual (paga solo cuando organiza)

### 2.2. Funcionalidades Incluidas en el Precio Base ($25.000)

Cada torneo incluye:
- Creación y configuración completa del torneo
- Gestión ilimitada de jugadores/parejas
- Generación automática de llaves y grupos
- Asignación de horarios y canchas
- Carga de resultados y avance automático
- Notificaciones push a jugadores (dentro de la app)
- Notificaciones por email
- Visualización en tiempo real para jugadores
- Historial del torneo
- Gestión de múltiples categorías
- Sin límite de participantes

### 2.3. Opciones de Expansión del Modelo (Futuro)

#### Plan Premium con Web Personalizada
- **Precio sugerido:** $15.000 - $20.000 adicionales por torneo o suscripción mensual de $40.000
- **Incluye:**
  - Subdominio personalizado (ej: `tuclub.pickletorneos.com`)
  - Página web con información del torneo
  - Galería de fotos
  - Sección de noticias/comunicados
  - Banners personalizados
  - Branding del club/organización

#### Notificaciones por WhatsApp
- **Costo del servicio:** ~$100 ARS por mensaje
- **Problema identificado:** Alto costo para notificación constante
- **Alternativa recomendada:** Mantener notificaciones push gratuitas como estándar
- **Implementación futura:** Solo si se logra negociar tarifas más bajas o para torneos premium

#### Otros Add-ons Potenciales
1. **Estadísticas Avanzadas** ($5.000/torneo)
   - Reportes detallados de rendimiento
   - Análisis de jugadores
   - Exportación de datos

2. **Diseño de Marketing** ($8.000/torneo)
   - Banners profesionales
   - Flyers digitales
   - Diplomas personalizados

3. **Gestión de Inscripciones con Fee** (Futuro - Fase 2)
   - Cobro de inscripciones a través de la app
   - Fee del 5-8% sobre cada inscripción
   - Gestión automática de pagos

---

## 3. Análisis de Costos

### 3.1. Costos Fijos Mensuales

| Concepto | Costo Mensual | Costo Anual |
|----------|---------------|-------------|
| Servidor (Hosting + BD + Storage) | $12.000 | $144.000 |
| Dominio | $2.917* | $35.000 |
| Notificaciones Push (Firebase) | $0 - $5.000** | $0 - $60.000 |
| Email (SendGrid/SMTP) | $0 - $3.000** | $0 - $36.000 |
| **TOTAL INFRAESTRUCTURA** | **~$15.000 - $20.000** | **~$180.000 - $240.000** |

*Prorrateado mensualmente
**Gratis en plan inicial, costo estimado al escalar

### 3.2. Costos Variables por Transacción

| Concepto | Costo por Torneo |
|----------|------------------|
| Comisión Mercado Pago (4.5% + IVA ≈ 5.5%)*** | $1.375 |
| Procesamiento adicional | $0 - $500 |
| **TOTAL por Torneo** | **~$1.375 - $1.875** |

***Sobre $25.000 de ingreso

### 3.3. Costos de Equipo (No incluidos - Variable)

- Desarrollo y mantenimiento
- Atención al cliente
- Marketing y adquisición
- *(Definir según estructura del equipo)*

---

## 4. Proyecciones Financieras

### 4.1. Escenario Conservador - Año 1

#### Fase 1: Meses 1-3 (Lanzamiento)
- **Organizadores activos:** 2
- **Torneos/mes por organizador:** 4 (1 por semana)
- **Total torneos/mes:** 8 torneos
- **Torneos gratis (primera vez):** -2 torneos
- **Torneos pagos/mes:** 6 torneos

**Ingresos mensuales:** 6 × $25.000 = **$150.000**
**Costos fijos mensuales:** $15.000 - $20.000
**Costos variables:** 6 × $1.875 = $11.250
**GANANCIA NETA MENSUAL:** **$118.750 - $123.750**

#### Fase 2: Meses 4-6
- **Organizadores activos:** 5
- **Torneos/mes:** 20 torneos
- **Torneos gratis:** -3 (nuevos organizadores)
- **Torneos pagos/mes:** 17 torneos

**Ingresos mensuales:** 17 × $25.000 = **$425.000**
**Costos fijos mensuales:** $18.000
**Costos variables:** 17 × $1.875 = $31.875
**GANANCIA NETA MENSUAL:** **$375.125**

#### Fase 3: Meses 7-12
- **Organizadores activos:** 10
- **Torneos/mes:** 40 torneos
- **Torneos gratis:** -5 (nuevos organizadores)
- **Torneos pagos/mes:** 35 torneos

**Ingresos mensuales:** 35 × $25.000 = **$875.000**
**Costos fijos mensuales:** $20.000
**Costos variables:** 35 × $1.875 = $65.625
**GANANCIA NETA MENSUAL:** **$789.375**

**RESUMEN AÑO 1:**
- **Ingresos anuales:** ~$5.250.000 ARS
- **Costos anuales:** ~$798.000 ARS
- **GANANCIA NETA AÑO 1:** ~$4.452.000 ARS

### 4.2. Escenario Optimista - Año 2

#### Proyección Año 2
- **Organizadores activos:** 25-30
- **Torneos/mes promedio:** 100-120 torneos
- **Torneos gratis (nuevos):** ~10/mes

**Ingresos mensuales estimados:** 100 × $25.000 = **$2.500.000**
**Costos mensuales totales:** ~$200.000
**GANANCIA NETA MENSUAL:** **~$2.300.000**

**RESUMEN AÑO 2:**
- **Ingresos anuales:** ~$30.000.000 ARS
- **GANANCIA NETA AÑO 2:** ~$27.600.000 ARS

---

## 5. Punto de Equilibrio

### Cálculo del Break-Even Point

**Costos fijos mensuales:** $18.000 (promedio)
**Margen por torneo:** $25.000 - $1.875 = $23.125

**Punto de equilibrio:** $18.000 ÷ $23.125 = **0.78 torneos/mes**

**Conclusión:** Necesitas vender **menos de 1 torneo por mes** para cubrir costos operativos básicos. Esto significa que el modelo es altamente rentable desde el inicio.

---

## 6. Estrategia de Adquisición

### 6.1. Fase Inicial (Meses 1-6)

**Objetivo:** 2-8 organizadores

**Tácticas:**
1. **Primer Torneo Gratis:** Elimina la barrera de entrada
2. **Marketing Directo:**
   - Contacto directo con complejos deportivos
   - Networking en torneos existentes
   - Demostración del producto en vivo
3. **Referencias:**
   - Programa de referidos (descuentos para organizadores que traigan nuevos usuarios)
4. **Presencia Local:**
   - Asistir a torneos y eventos deportivos
   - Acuerdos con complejos de pádel

### 6.2. Fase de Crecimiento (Meses 7-24)

**Objetivo:** 10-30 organizadores

**Tácticas:**
1. **Marketing Digital:**
   - Google Ads (búsquedas: "sistema gestión torneos pádel")
   - Facebook/Instagram Ads (targeting organizadores deportivos)
   - SEO para búsquedas orgánicas
2. **Content Marketing:**
   - Blog con tips de organización de torneos
   - Casos de éxito de organizadores
   - Videos tutoriales
3. **Partnerships:**
   - Alianzas con federaciones de pádel
   - Acuerdos con marcas deportivas
   - Convenios con cadenas de complejos

---

## 7. Estrategia de Precios y Competencia

### 7.1. Análisis de Competencia

**Referencia:** Padel Organizer (padelorganizer.com.ar)

**Ventajas competitivas de PickleTorneos:**
- Modelo de pago por uso (vs suscripciones)
- Primer torneo gratis
- App móvil moderna para jugadores
- Experiencia de usuario mejorada
- Soporte local y personalizado

### 7.2. Justificación del Precio ($25.000/torneo)

**Valor generado al organizador:**
- Ahorro de 5-10 horas de trabajo administrativo
- Costo/hora del organizador estimado: $3.000-5.000/hora
- **Valor generado:** $15.000 - $50.000 por torneo
- **ROI para el organizador:** 60% - 200%

**Comparativa con alternativas:**
- Gestión manual (Excel + WhatsApp): Gratis pero consume 10+ horas
- Software de suscripción: $20.000-50.000/mes (más caro si organiza pocos torneos)
- **PickleTorneos:** Paga solo cuando organiza, precio justo por torneo

---

## 8. Fuentes de Ingreso Futuras (Roadmap)

### Fase 2 (Año 2-3)

#### 8.1. Gestión de Inscripciones + Fee
- **Modelo:** Cobro de inscripciones a través de la plataforma
- **Fee:** 5-8% sobre cada inscripción
- **Ejemplo:**
  - Torneo con 32 parejas
  - Inscripción: $5.000 por pareja
  - Ingreso total del torneo: $160.000
  - Fee 6%: **$9.600 por torneo** (adicional a los $25k)

#### 8.2. Plan Premium con Web
- **Precio:** $40.000/mes o $15.000 adicionales por torneo
- **Target:** Organizadores profesionales o clubes grandes
- **Funcionalidades:** Web personalizada, branding, galería

#### 8.3. Publicidad y Sponsors
- **Modelo:** Espacios publicitarios en la app
- **Target:** Marcas deportivas (raquetas, pelotas, indumentaria)
- **Ingreso estimado:** $50.000 - $200.000/mes (según reach)

#### 8.4. Expansión a Otros Deportes
- Fútbol 5/7
- Tenis
- Volleyball
- Basketball
- **Estrategia:** Mismo modelo de cobro, multiplicar mercado

---

## 9. Riesgos y Mitigación

### 9.1. Riesgos Identificados

| Riesgo | Probabilidad | Impacto | Mitigación |
|--------|--------------|---------|------------|
| Baja adopción inicial | Media | Alto | Primer torneo gratis + marketing directo agresivo |
| Competencia de software gratis | Media | Medio | Enfoque en UX superior y soporte local |
| Problemas técnicos en torneos | Baja | Alto | Testing exhaustivo + soporte técnico 24/7 durante torneos |
| Estacionalidad (menos torneos en invierno) | Alta | Medio | Diversificar deportes + expandir geográficamente |
| Costos de servidor al escalar | Media | Bajo | Arquitectura escalable + monitoreo de costos |

### 9.2. Plan de Contingencia

- **Reserva de capital:** Mantener 6 meses de costos fijos como colchón
- **Pricing flexible:** Capacidad de ajustar precios según feedback del mercado
- **Pivot rápido:** Disposición a modificar funcionalidades según necesidad real

---

## 10. KPIs y Métricas Clave

### 10.1. Métricas de Producto

- **Torneos creados/mes**
- **Organizadores activos/mes**
- **Tasa de conversión (gratis → pago):** Objetivo >80%
- **Torneos promedio por organizador/mes**
- **Jugadores activos en la app**
- **Tasa de retención de organizadores:** Objetivo >90%

### 10.2. Métricas Financieras

- **MRR (Monthly Recurring Revenue):** Ingresos mensuales
- **CAC (Customer Acquisition Cost):** Costo de adquirir un organizador
- **LTV (Lifetime Value):** Valor de un organizador en su vida útil
- **Ratio LTV/CAC:** Objetivo >3:1
- **Margen bruto:** Objetivo >85%
- **Burn rate:** Tasa de gasto mensual

### 10.3. Métricas de Crecimiento

- **Tasa de crecimiento mensual de organizadores**
- **Tasa de referidos (organizadores que traen nuevos organizadores)**
- **NPS (Net Promoter Score):** Satisfacción del usuario

---

## 11. Proyección a 3 Años

### Resumen Financiero

| Período | Organizadores | Torneos/Mes | Ingresos/Mes | Ganancia/Mes | Ganancia Anual |
|---------|---------------|-------------|--------------|--------------|----------------|
| **Año 1 (Inicio)** | 2-10 | 8-40 | $150k-$875k | $120k-$790k | ~$4.5M |
| **Año 2** | 25-30 | 100-120 | $2.5M | $2.3M | ~$27.6M |
| **Año 3** | 50-80 | 200-320 | $5M-$8M | $4.6M-$7.5M | ~$55M-$90M |

**Nota:** Las proyecciones asumen crecimiento orgánico sin inversión significativa en marketing. Con inversión en marketing, los números podrían ser 2-3x mayores.

---

## 12. Conclusiones y Recomendaciones

### 12.1. Fortalezas del Modelo

✅ **Punto de equilibrio muy bajo** (menos de 1 torneo/mes)
✅ **Margen de ganancia alto** (~92% por torneo)
✅ **Modelo escalable** (costos crecen lentamente vs ingresos)
✅ **Barrera de entrada baja** (primer torneo gratis)
✅ **Propuesta de valor clara** para organizadores
✅ **Múltiples fuentes de ingreso futuras**

### 12.2. Recomendaciones Estratégicas

1. **Enfoque inicial en adquisición:** Los primeros 10 organizadores son críticos
2. **Perfeccionar el producto antes de escalar:** Asegurar excelente UX
3. **Construcción de comunidad:** Crear red de organizadores que se refieran entre sí
4. **Documentar casos de éxito:** Usar testimonios reales para marketing
5. **Mantener simplicidad en pricing:** No complicar con muchos planes al inicio
6. **Preparar infraestructura para escala:** Anticipar crecimiento en costos de servidor
7. **Considerar fee sobre inscripciones como prioridad Fase 2:** Alto potencial de ingresos adicionales

### 12.3. Próximos Pasos

1. ✅ **Validar MVP** con 2 organizadores iniciales (en curso)
2. 🎯 **Conseguir 5-10 organizadores** en primeros 6 meses
3. 🎯 **Lograr 100+ torneos gestionados** en primer año
4. 🎯 **Implementar sistema de inscripciones con fee** en Año 2
5. 🎯 **Expandir a 3+ provincias/países** en Año 2-3
6. 🎯 **Diversificar a otros deportes** en Año 3

---

## 13. Apéndice: Supuestos y Consideraciones

### Supuestos Utilizados en Proyecciones

- Precio del torneo: $25.000 ARS (fijo, sin ajuste inflacionario)
- Tasa de conversión gratis→pago: 85%
- Promedio de torneos por organizador: 4/mes
- Comisión Mercado Pago: 5.5%
- Tasa de retención de organizadores: 90%
- Crecimiento orgánico sin inversión significativa en marketing

### Consideraciones para Actualización

Este documento debe revisarse y actualizarse:
- **Trimestralmente:** Durante el primer año
- **Semestralmente:** A partir del segundo año
- **Cuando ocurran cambios significativos:** Nuevas funcionalidades, cambios de precio, pivotes de modelo

---

**Última actualización:** Octubre 2025
**Versión:** 1.0
