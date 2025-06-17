
package br.com.unicuritiba.bancocentral.controller;

import br.com.unicuritiba.bancocentral.model.Loan;
import br.com.unicuritiba.bancocentral.service.LoanService;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.List;

@RestController
@RequestMapping("/api/loans")
public class LoanController {

    private final LoanService service;

    public LoanController(LoanService service) {
        this.service = service;
    }

    @GetMapping
    public List<Loan> findAll() {
        return service.findAll();
    }

    @GetMapping("/{id}")
    public ResponseEntity<Loan> findById(@PathVariable Long id) {
        return service.findById(id)
                .map(ResponseEntity::ok)
                .orElse(ResponseEntity.notFound().build());
    }

    @PostMapping
    public Loan create(@RequestBody Loan obj) {
        return service.save(obj);
    }

    @PutMapping("/{id}")
    public ResponseEntity<Loan> update(@PathVariable Long id, @RequestBody Loan obj) {
        return service.findById(id)
                .map(record -> {
                    obj.setId(id);
                    return ResponseEntity.ok().body(service.save(obj));
                })
                .orElse(ResponseEntity.notFound().build());
    }

    @DeleteMapping("/{id}")
    public ResponseEntity<Void> delete(@PathVariable Long id) {
        return service.findById(id)
                .map(record -> {
                    service.delete(id);
                    return ResponseEntity.ok().<Void>build();
                })
                .orElse(ResponseEntity.notFound().build());
    }
}
