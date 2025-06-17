
package br.com.unicuritiba.bancocentral.service;

import br.com.unicuritiba.bancocentral.model.Loan;
import br.com.unicuritiba.bancocentral.repository.LoanRepository;

import org.springframework.stereotype.Service;

import java.util.List;
import java.util.Optional;


@Service
public class LoanService {
    private final LoanRepository repository;

    public LoanService(LoanRepository repository) {
        this.repository = repository;
    }

    public List<Loan> findAll() {
        return repository.findAll();
    }

    public Optional<Loan> findById(Long id) {
        return repository.findById(id);
    }

    public Loan save(Loan obj) {
        return repository.save(obj);
    }

    public void delete(Long id) {
        repository.deleteById(id);
    }
}
