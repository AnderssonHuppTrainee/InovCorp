# 📦 Histórico da Análise – Outubro/2025

Documentação consolidada da revisão conduzida em outubro de 2025 sobre o projeto **Smart Management**. Este resumo substitui os relatórios espalhados anteriormente em vários arquivos na raiz do repositório.

---

## 1. Contexto

- **Período da avaliação:** 7 a 17 de outubro de 2025  
- **Escopo:** 73 páginas Inertia, ~180 componentes Vue, 5 composables ativos  
- **Objetivos:** reduzir duplicação, padronizar experiências de formulário/listagem, preparar migração para novos módulos.

---

## 2. Principais Conclusões

| Tema                       | Situação em 2025-10 | Observações atuais |
| ------------------------- | ------------------- | ------------------ |
| Código duplicado          | ~1.600 linhas       | Redução via wrappers/composables segue prioridade |
| Formatação monetária      | Lógica repetida     | `useMoneyFormatter` implantado posteriormente |
| Componentização de forms  | Inexistente         | `FormWrapper`, `IndexWrapper`, `ShowWrapper` publicados |
| Consistência de checkbox  | Abordagens mistas   | `CheckboxField` revisado e padronizado |
| Testes automáticos        | Cobertura parcial   | Suites Pest criadas, mas relatórios exigem atualização |

---

## 3. Quick Wins Aplicados

1. **Formatter utilities** – criação de `useMoneyFormatter` e `useDateFormatter` para substituir `toFixed` e `Intl` duplicados.  
2. **CheckboxField** – componente encapsula `FormField` e normaliza integração com vee-validate.  
3. **Wrapper components** – padronização de formulários, listagens e telas de detalhe (`FormWrapper`, `IndexWrapper`, `ShowWrapper`), alcançando ~95% das páginas Index e ~2.100 linhas removidas.  
4. **Linhas removidas** – estimativa inicial de ~2.100 linhas eliminadas após migrações de Settings/Core.

---

## 4. Roadmap Original (2025-10)

| Fase | Foco                        | Status em 2025-10 | Notas posteriores |
| ---- | --------------------------- | ----------------- | ----------------- |
| 2A   | Settings                    | 47% concluído      | Posteriormente finalizado |
| 2B   | Core Business (entities, proposals, work-orders) | Planejado | Wrappers já presentes em `resources/js/pages` |
| 2C   | Access Management           | Planejado          | Necessita confirmar consistência |
| 2D   | Financial                   | Planejado          | Verificar adequação de uploads |

---

## 5. Recomendações Registradas

- **Manter documentação viva** no README principal e em um índice único (este arquivo).  
- **Migrar checklists temporários** para issues do Git ou boards Kanban.  
- **Produzir relatórios de testes automatizados** via comandos (`php artisan test`, `npm run test`) ao invés de artefatos estáticos.

---

## 6. Testes Automatizados (registos 2025-10)

- Houve duas medições conflitantes em 13/10/2025: uma parcial (9/20 Pest tests passando) e outra proclamando 66/66 unit tests verdes.  
- Muitas falhas relatadas decorriam de factories incompletas (falta de `client_id`, `assigned_to`, etc.), desde então ajustadas no código.  
- **Ação recomendada:** executar a suite atual (`php artisan test` ou `vendor/bin/pest`) para obter números confiáveis e, se necessário, gerar relatórios automatizados (CI, cobertura HTML).

---

## 7. Artefatos Relacionados

A documentação específica removida foi arquivada em commits anteriores do repositório. Principais referências históricas:

- Sumário executivo para decisão de investimento (`SUMARIO_EXECUTIVO.md`)  
- Guia de testes e dados seed (`README_TESTE.md`)  
- Component wrapper (`resources/js/components/common/FormWrapper.vue`)  
- Ajustes de checkbox (`resources/js/components/common/CheckboxField.vue`)

---

### Manutenção

- Atualizar este arquivo sempre que houver novas análises estruturais de grande porte.  
- Referenciar novos relatórios com data no formato `docs/archive/YYYY-MM`.  
- Evitar reintroduzir checklists ou relatórios temporários na raiz do projeto.


